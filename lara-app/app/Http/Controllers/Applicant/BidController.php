<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\BidStatusEnum;
use App\Enums\BidTypeEnum;
use App\Enums\LinkedMethodStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterBidRequest;
use App\Models\Bid as BidModel;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BidController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/applicant/requests/bid/register/{requestId}",
     *     summary="Register new sell/buy bid",
     *     tags={"Bids"},
     *     operationId="registerBid",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="requestId",
     *         in="path",
     *         description="ID of the request which the applicant tend to register bid on",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="bid_rate", type="number"),
     *             @OA\Property(property="payment_method_id", type="number"),
     *             @OA\Property(property="description", type="string"),
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
     *         description="Request not found or linked method not found for the requester/bidder",
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
    public function register(RegisterBidRequest $request, $requestId){

        $applicant = Auth::user();

        $validated_credentials = $request->validated();
        $bid_rate = $validated_credentials['bid_rate'];
        $payment_method_id = $validated_credentials['payment_method_id'];

        // Check the request exists and the bidder applicant is not the applicant who owns the request
        $req = RequestModel::find($requestId);
        if(!$req || $req->status === RequestStatusEnum::Removed || $req->user->id === $applicant->id) {
            return response(['message' => 'Request not found or the current applicant is the owner of the target request.'], 404);
        }

        // Check the specified input payment method exists in request payment methods list
        $request_payment_methods = $req->getRequestPaymentMethods();
        $paymentMethodExists = $request_payment_methods->pluck('id')->contains($payment_method_id);
        if (!$paymentMethodExists) {
            return response(['message' => 'The input payment method id not exists in request payment methods.'], 422);
        }

        // Check the applicant has already linked the specified input payment method
        $bidder_linked_method = $applicant->linkedMethods()->where('method_type_id', $payment_method_id)->where('status', LinkedMethodStatusEnum::Active)->first();
        if(!$bidder_linked_method) {
            return response(['message' => 'No linked method by the input payment method id found for the bidder applicant.'], 404);
        }

        // Check the feasibility threshold meeting
        if($bid_rate < $req->lower_bound_feasibility_threshold || $bid_rate > $req->upper_bound_feasibility_threshold) {
            return response(['message' => 'The bid_rate must be greater than the lower bound and less than the upper bound feasibility threshold.'], 422);
        }

        $top_bid = $req->getTopBid();
        $target_account_id = '';
        // Check appropriate validations on sell request
        if($req->type === RequestTypeEnum::Sell) {
            if(($top_bid && $top_bid->bid_rate > $bid_rate) || $bid_rate < $req->request_rate) {
                return response(['message' => 'The bid_rate must be equal to or greater than both the request rate and the current active top bid.'], 422);
            }
            $target_account_id = $bidder_linked_method->id; // Must be set to bidder linked method as the destination account
        }

        // Check appropriate validations on buy request
        if($req->type === RequestTypeEnum::Buy) {
            if(($top_bid && $top_bid->bid_rate < $bid_rate) || $bid_rate > $req->request_rate) {
                return response(['message' => 'The bid_rate must be equal to or less than both the request rate and the current active top bid.'], 422);
            }

            $requester_linked_method = $req->linkedMethods()->where('method_type_id', $payment_method_id)->where('status', LinkedMethodStatusEnum::Active)->first();
            if(!$requester_linked_method) {
                return response(['message' => 'No linked method by the input payment method id found for the requester applicant.'], 404);
            }
            $target_account_id = $requester_linked_method->id; // Must be set to requester linked method as the destination account
        }

        // Store the new bid on database
        $new_bid = $req->bids()->create([
            'type' => $req->type == RequestTypeEnum::Sell ? BidTypeEnum::Buy : BidTypeEnum::Sell,
            'support_id' => Str::uuid(),
            'bid_rate' => $validated_credentials['bid_rate'],
            'description' => isset($validated_credentials['description'])? $validated_credentials['description']: Null,
            'status' => BidStatusEnum::Registered,
            'applicant_id' => $applicant->id,
            'target_account_id' => $target_account_id
        ]);

        if($new_bid instanceof BidModel) {

            // Set the support_id using the pattern 'BI' + id
            $new_bid->update(['support_id' => config('constants.SupportId_Prefixes.Bid_Pr') . $new_bid->id]);

            // Set registered bid as the top bid
            $req->setTopBid($new_bid->id);

            return response(['message' => 'New bid registered successfully.'], 200);
        }
        else {
            return response(['message' => 'An error occurred while registering the new bid.'], 500);
        }
    }
}
