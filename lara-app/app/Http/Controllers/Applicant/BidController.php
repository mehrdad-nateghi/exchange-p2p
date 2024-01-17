<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcceptBidRequest;
use App\Models\Bid;
use App\Models\Trade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BidController extends Controller
{

    // This service will be used for doing bid acceptance process, serves in related APIs
    public function acceptBidService($request, $bid) {

        DB::beginTransaction();

        try {

            // Proceed with the bid acceptance process
            $request->acceptBid($bid);

            // Create a trade entry
            $trade = Trade::create([
                'support_id' => Str::uuid(),
                'trade_fee' => 0,
                'request_id' => $request->id,
                'bid_id' =>$bid->id,
                'status' => TradeStatusEnum::RialPending,
            ]);

            if ($trade) {
                // Set the support_id using the pattern 'TR' + id
                $trade->update([
                    'support_id' => config('constants.SupportId_Prefixes.Trade_Pr') . $trade->id
                ]);

                // Set system fee
                $system_fee_set_status = $trade->setSystemFee();
                if (!$system_fee_set_status) {
                    throw new \Exception("An error occurred while confirming the bid, specifically setting the system fee to the trade.");
                }

                // Change the request's status
                $request->update([
                    'status' => RequestStatusEnum::InTrade
                ]);

                DB::commit();

                return [
                    'status' => 200,
                    'message' => "Bid confirmed successfully."
                ];
            }
            else {
                throw new \Exception("An error occurred while confirming the bid, specifically creating the trade.");
            }
        } catch (\Exception $e) {

            DB::rollBack();

            return [
                'status' => 500,
                'message' => "An error occurred while confirming the bid."
            ];
        }
    }

    /**
     * @OA\Post(
     *     path="/api/applicant/bids/accept",
     *     summary="Accept a bid for the request registered by authenticated applicant",
     *     tags={"Bids"},
     *     operationId="acceptBidByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="bid_id", type="number"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request.")
     *      )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bid not found or not associated with any requests of the applicant",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function acceptBid(AcceptBidRequest $request){

        $applicant = Auth::user();
        $validated_data = $request->validated();
        $bid_id = $validated_data['bid_id'];

        // Check whether the bid exists and valid
        $bid = Bid::where('id', $bid_id)
            ->whereNotIn('status',[BidStatusEnum::Invalid, BidStatusEnum::Rejected])
            ->first();

        if (!$bid || !$applicant->requests()->where('id', $bid->request_id)->whereNotIn('status',[RequestStatusEnum::Removed, RequestStatusEnum::InTrade])->exists()) {
            return response(['message' => 'Bid not found or not associated with any request of the applicant.'], 404);
        }

        $bid_acceptance = $this->acceptBidService($bid->request, $bid);

        return response(['message' => $bid_acceptance['message']],$bid_acceptance['status']);

    }

}
