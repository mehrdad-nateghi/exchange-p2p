<?php

namespace App\Repositories;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use App\Interfaces\BidRepositoryInterface;
use App\Models\Bid;
use App\Models\Request;
use App\Models\Trade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class BidRepository implements BidRepositoryInterface
{
    private $tradeRepository;

    public function __construct(TradeRepository $tradeRepository)
    {
        $this->tradeRepository = $tradeRepository;
    }

    /**
     * Confirm a specific bid for the associated request
     */
    public function confirmBid(Request $request, Bid $bid)
    {
        DB::beginTransaction();

        try {

            // Update statuses of the bids of the request
            $request->bids()->where('id',$bid->id)->update([
                'status' => BidStatusEnum::Confirmed
            ]);

            $request->bids()->whereIn('status',[BidStatusEnum::Top, BidStatusEnum::Registered])->update([
                'status' => BidStatusEnum::Rejected
            ]);

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
                $system_fee_set_status = $this->tradeRepository->setSystemFee($trade);
                if (!$system_fee_set_status) {
                    throw new \Exception("An error occurred while confirming the bid, specifically setting the system fee to the trade.");
                }

                // Update the request's status
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
                'message' => $e->getMessage()
            ];
        }

    }

    /**
     * Check and perform automatic bid confirmation once the conditions are met
     */
    public function autoConfirmBid(Request $request, Bid $bid)
    {
        // Check potential of auto bid confirmation
        if(($request->type == RequestTypeEnum::Sell && $request->acceptance_threshold > $bid->bid_rate) || ($request->type == RequestTypeEnum::Buy && $request->acceptance_threshold < $bid->bid_rate)) {
            return [
                'status' => 422,
                'message' => 'Auto bid confirmation denied.'
            ];
        }

        return $this->confirmBid($request, $bid);
    }

}
