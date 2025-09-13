<?php

namespace App\Http\Controllers\API\V1\Trades\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Trade\Admin\IndexTradeRequest;
use App\Http\Resources\Trades\Admin\TradeCollection;
use App\Models\Trade;
use App\QueryFilters\Admin\TradeStatusFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexTradeController extends Controller
{
    public function __invoke(
        IndexTradeRequest $request
    ): JsonResponse
    {
        //dd($request->all());
        try {
            $trades = QueryBuilder::for(Trade::class)
                ->with([
                    'tradeSteps',
                    'invoices.transactions',
                    'request.user',
                    'request.paymentMethods',
                    'bid.user',
                    'bid.paymentMethod'
                ])
                ->select('trades.*')
                ->allowedFilters([
                    AllowedFilter::custom('status', new TradeStatusFilter()),
                    'number'
                ])
                ->defaultSort('-created_at')
                ->allowedSorts('created_at')
                ->paginateWithDefault();
            /*
            $trades = QueryBuilder::for(Trade::class)
                ->with(['tradeSteps' => function($query) {
                    $query->select('*');
                }])
                ->with(['invoices' => function($query) {
                    $query->select('*');
                }])
                ->with(['request.user' => function($query) {
                    $query->select('*');
                }])
                ->with(['request.paymentMethods' => function($query) {
                    $query->select('*');
                }])
                ->with(['bid.user' => function($query) {
                    $query->select('*');
                }])
                ->with(['bid.paymentMethod' => function($query) {
                    $query->select('*');
                }])
                ->select([
                    'trades.*',
                ])
                ->allowedFilters([
                    AllowedFilter::custom('status', new TradeStatusFilter()),
                    'number'
                ])
                ->defaultSort(['-created_at'])
                ->allowedSorts('created_at')
                ->paginateWithDefault();*/

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
