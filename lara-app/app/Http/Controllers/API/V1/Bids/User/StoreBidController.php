<?php

namespace App\Http\Controllers\API\V1\Bids\User;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Events\BidStoredEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\StoreBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Step;
use App\Services\API\V1\BidService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreBidController extends Controller
{
    public function __invoke(
        StoreBidRequest $request,
        BidService $bidService,
    ): JsonResponse
    {
        try {
            DB::beginTransaction();

            $bid = $bidService->create([
                'user_id' => Auth::id(),
                'request_id' => $request->input('request_id'),
                'payment_method_id' => $request->input('payment_method_id'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
            ]);

            $mustAcceptBid = $request->input('must_accept_bid');

            if($mustAcceptBid){
                // update bid
                $bid = $bidService->acceptBid($bid);

                // update request
                $bid->request()->update([
                    'status' => RequestStatusEnum::TRADING
                ]);

                // create trade
                $trade = $bid->trade()->create([
                    'request_id' => $bid->request_id,
                    'status' => TradeStatusEnum::PROCESSING->value,
                ]);

                // create trade steps
                $steps = Step::all();

                $stepsData = $steps->map(function ($step) {
                    return [
                        'name' => $step->name,
                        'description' => $step->description,
                        'priority' => $step->priority,
                        'owner' => $step->owner,
                        'status' => $step->name === 'Pay Toman to System' ? TradeStepsStatusEnum::DOING->value : TradeStepsStatusEnum::TODO->value,
                        'duration_minutes' => $step->duration_minutes,
                        'expire_at' => $step->name === 'Pay Toman to System' ? Carbon::now()->addMinute($step->duration_minutes) : null,
                    ];
                })->toArray();

                $trade->tradeSteps()->createMany($stepsData);
                $request = $bid->request;

                // create invoice for trade
                $amount = $bid->refresh()->price * $request->volume;
                $feePercentage = config('constants.invoice_fee_percentage');
                $fee = round($amount * ($feePercentage / 100), 2);
                $feeForeign = round($request->volume * ($feePercentage / 100), 2);

                // create invoices for buyer
                $buyerUserId = $request->type->value == RequestTypeEnum::BUY->value ? $request->user->id : $bid->user->id;

                $trade->refresh()->invoices()->create([
                    'user_id' => $buyerUserId,
                    'amount' => $amount,
                    'fee' => $fee,
                    'fee_foreign' => $feeForeign,
                    'status' => InvoiceStatusEnum::PENDING->value,
                    'type' => InvoiceTypeEnum::STEP_ONE_PAY_TOMAN_TO_SYSTEM->value,
                ]);

                // create invoices for seller
                $sellerUserId = $request->type->value == RequestTypeEnum::SELL->value ? $request->user->id : $bid->user->id;

                $trade->refresh()->invoices()->create([
                    'user_id' => $sellerUserId,
                    'amount' => $amount,
                    'fee' => $fee,
                    'fee_foreign' => $feeForeign,
                    'status' => InvoiceStatusEnum::PENDING->value,
                    'type' => InvoiceTypeEnum::PAY_TOMAN_TO_SELLER->value,
                ]);
            }

            $resource = new BidResource($bid->refresh());

            event(new BidStoredEvent($bid, $mustAcceptBid));

            DB::commit();

            ///// Notifications /////
//            $requester = $bid->request;
//            $otherBidders = $bid->otherBidders;
//            $bidder = $bid->user;
//
//            if($request->input('must_accept_bid')){
//                // Notify Requester
//                Notification::send(
//                    $requester,
//                    new BidAcceptedAutomaticNotification($bid, BidRegisteredNotificationSendToEnum::REQUESTER->value)
//                );
//
//                // Notify Bid Winner
//                Notification::send(
//                    $bidder,
//                    new BidAcceptedAutomaticNotification($bid, BidRegisteredNotificationSendToEnum::BIDDER_WINNER->value)
//                );
//
//                // Notify Other Bidders
//                if(!empty($otherBidders)){ // if empty? it's the first bid
//                    Notification::send(
//                        $otherBidders,
//                        new BidAcceptedAutomaticNotification($bid, BidRegisteredNotificationSendToEnum::OTHER_BIDDERS->value)
//                    );
//                }
//            }else{
//                // Notify requester
//                Notification::send(
//                    $requester,
//                    new BidRegisteredNotification($bid, BidRegisteredNotificationSendToEnum::REQUESTER->value)
//                );
//
//                // Notify Other Bidders
//                if(!empty($otherBidders)){ // if empty? it's the first bid
//                    Notification::send(
//                        $otherBidders,
//                        new BidRegisteredNotification($bid, BidRegisteredNotificationSendToEnum::OTHER_BIDDERS->value)
//                    );
//                }
//            }

            return apiResponse()
                ->message(trans('api-messages.create_success', ['attribute' => trans('api-messages.bid')]))
                ->created()
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
