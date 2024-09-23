<?php

namespace App\Http\Controllers\API\V1\Trades\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Trades\Admin\TradeResource;
use App\Models\Trade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShowTradeController extends Controller
{
    public function __invoke(
        Trade $trade,
    ): JsonResponse {
        try {
            $resource =  new TradeResource($trade->load('tradeSteps','invoices'));

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.trade')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
