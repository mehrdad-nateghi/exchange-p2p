<?php

namespace App\Http\Controllers\API\V1\TradeSteps\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\TradeStep\Admin\IncreaseExpireAtTradeStepRequest;
use App\Http\Resources\TradeStepResource;
use App\Models\TradeStep;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class IncreaseExpireAtTradeStepController extends Controller
{
    public function __invoke(
        IncreaseExpireAtTradeStepRequest $request,
        TradeStep $tradeStep,
    ): JsonResponse
    {
        try {
            $tradeStep->update([
                'expire_at' => Carbon::now()->addHour($request->hours),
            ]);

            $resource = new TradeStepResource($tradeStep->refresh());

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.trade_steps')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
