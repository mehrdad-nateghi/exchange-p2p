<?php

namespace App\Http\Controllers\API\V1\Trades\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Trade\User\IndexTradeRequest;
use App\Http\Resources\TradeCollection;
use App\QueryFilters\TradeStatusFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexTradeController extends Controller
{
    public function __invoke(
        IndexTradeRequest $request
    ): JsonResponse
    {
        try {
            $cacheKey = 'all_trades_' . $request->fullUrl();

            /*$value = Cache::get('all_trades_' . $request->fullUrl());
            if (!is_null($value)) {
                dd($value);
            }*/

            return Cache::remember($cacheKey, 3600, function () use ($request) {
                $trades = QueryBuilder::for(Auth::user()->trades())
                    ->with(['tradeSteps' => function($query) {
                        $query->select('*');
                    }])
                    ->with(['invoices' => function($query) {
                        $query->select('*');
                    }])
                    ->with(['bid.request', 'bid.paymentMethod'])
                    ->select([
                        'trades.*',
                    ])
                    ->allowedFilters([
                        AllowedFilter::custom('status', new TradeStatusFilter),
                    ])
                    ->defaultSort(['-created_at'])
                    ->allowedSorts('created_at')
                    ->paginateWithDefault();

                $trades = new TradeCollection($trades);

                return apiResponse()
                    ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.trades')]))
                    ->data($trades)
                    ->getApiResponse();
            });
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }

//        try {
//            $trades = QueryBuilder::for(Auth::user()->trades())
//                ->with(['tradeSteps' => function($query) {
//                    $query->select('*');
//                }])
//                ->with(['invoices' => function($query) {
//                    $query->select('*');
//                }])
//                ->with(['bid.request', 'bid.paymentMethod']) // Nested eager loading
//                ->select([
//                    'trades.*',
//                ])
//                ->allowedFilters([
//                    AllowedFilter::custom('status', new TradeStatusFilter),
//                ])
//                ->defaultSort(['-created_at'])
//                ->allowedSorts('created_at')
//                ->paginateWithDefault();
//
//            $trades = new TradeCollection($trades);
//
//            return apiResponse()
//                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.trades')]))
//                ->data($trades)
//                ->getApiResponse();
//        } catch (\Throwable $t) {
//            Log::error($t);
//            return internalServerError();
//        }
    }
}
