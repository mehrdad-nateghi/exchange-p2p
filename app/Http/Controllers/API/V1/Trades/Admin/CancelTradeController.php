<?php

namespace App\Http\Controllers\API\V1\Trades\Admin;

use App\Enums\RequestStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Trades\Admin\TradeResource;
use App\Models\Trade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CancelTradeController extends Controller
{
    public function __invoke(
        Trade $trade,
    ): JsonResponse
    {
        try {
            $trade->update([
                'status' => TradeStatusEnum::CANCELED,
                'canceled_at' => now(),
            ]);

            $trade->request()->update(['status' => RequestStatusEnum::CANCELED]);

            $resource = new TradeResource($trade->load('tradeSteps', 'invoices'));

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.trade')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
