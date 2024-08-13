<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Enums\RequestStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\UpdateBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Bid;
use App\Models\Request;
use App\Models\Step;
use App\Services\API\V1\BidService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRequestController extends Controller
{
    public function __invoke(
        //UpdateBidRequest $request,
        Request $request,
        //BidService $bidService
    ): JsonResponse {

        try {
            DB::beginTransaction();

            dd($request->all());

            /*// update bid
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

            $resource = new BidResource($bid);*/

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
