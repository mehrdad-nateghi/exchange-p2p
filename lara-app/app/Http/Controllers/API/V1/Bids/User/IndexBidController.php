<?php

namespace App\Http\Controllers\API\V1\Bids\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\IndexBidRequest;
use App\Http\Requests\API\V1\Request\User\IndexRequestRequest;
use App\Http\Resources\BidCollection;
use App\Http\Resources\RequestCollection;
use App\QueryFilters\BidStatusFilter;
use App\QueryFilters\RequestPaymentMethodFilter;
use App\QueryFilters\RequestStatusFilter;
use App\QueryFilters\RequestTypeFilter;
use App\QueryFilters\RequestVolumeFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexBidController extends Controller
{
    public function __invoke(
        IndexBidRequest $request
    ): JsonResponse
    {
        try {
            $bids = QueryBuilder::for(Auth::user()->bids())
                ->select([
                    'bids.created_at',
                    'bids.number',
                    'bids.price',
                    'bids.ulid',
                    'bids.status',
                    'bids.updated_at',
                    'bids.request_id',
                ])
                ->allowedFilters([
                    AllowedFilter::custom('status', new BidStatusFilter)
                ])
                ->defaultSort(['-created_at'])
                ->allowedSorts('created_at')
                ->paginateWithDefault();

            $bids = new BidCollection($bids);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.bids')]))
                ->data($bids)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
