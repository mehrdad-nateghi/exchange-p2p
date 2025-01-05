<?php

namespace App\Http\Controllers\API\V1\Bids\User;

use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\UpdateBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Bid;
use App\Models\Step;
use App\Services\API\V1\BidService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBidController extends Controller
{
    public function __invoke(
        UpdateBidRequest $request,
        Bid $bid,
        BidService $bidService
    ): JsonResponse {

        try {
            DB::beginTransaction();

            // update bid
            $bid = $bidService->acceptBid($bid);

            // update request
            $request = $bid->request()->update([
               'status' => RequestStatusEnum::TRADING
            ]);

            // create trade
            $trade = $bid->trades()->create([
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

            // create invoices
            $amount = $bid->refresh()->price * $request->volume;
            $feePercentage = config('constants.invoice_fee_percentage');
            $fee = round($amount * ($feePercentage / 100), 2);
            $feeForeign = round($request->volume * ($feePercentage / 100), 2);


            //$userId = $request->type->value == RequestTypeEnum::BUY->value ? $request->user_id : $bid->user_id;

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

            $resource = new BidResource($bid);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.bid')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
