<?php

namespace App\Http\Controllers\API\V1\Trades\Guest;

use App\Enums\TradeStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\Guest\IndexRequestRequest;
use App\Http\Resources\RequestCollection;
use App\Http\Resources\TradeCollection;
use App\Models\Request;
use App\Models\Trade;
use App\QueryFilters\RequestPaymentMethodFilter;
use App\QueryFilters\RequestStatusFilter;
use App\QueryFilters\RequestTypeFilter;
use App\QueryFilters\RequestVolumeFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexTradeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $trades = Trade::whereIn('status', [
                TradeStatusEnum::PROCESSING->value,
                TradeStatusEnum::COMPLETED->value,
            ])
                ->orderBy('created_at', 'desc')
                ->paginate();

            $trades = new TradeCollection($trades);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.trades')]))
                ->data($trades)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
