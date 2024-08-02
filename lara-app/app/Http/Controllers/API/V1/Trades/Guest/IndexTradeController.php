<?php

namespace App\Http\Controllers\API\V1\Trades\Guest;

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
    public function __invoke(
        //IndexRequestRequest $request
    ): JsonResponse
    {
        try {
            /*$requests = QueryBuilder::for(Request::class)
                ->allowedFilters([
                    AllowedFilter::custom('type', new RequestTypeFilter),
                    AllowedFilter::custom('status', new RequestStatusFilter),
                    AllowedFilter::custom('payment_method', new RequestPaymentMethodFilter),
                    AllowedFilter::custom('volume_from', new RequestVolumeFilter),
                    AllowedFilter::custom('volume_to', new RequestVolumeFilter),
                ])
                ->defaultSort(['-created_at','-price'])
                ->allowedSorts('created_at','price')
                ->paginateWithDefault();*/

            $trades = Trade::orderBy('created_at', 'desc')->paginate();

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
