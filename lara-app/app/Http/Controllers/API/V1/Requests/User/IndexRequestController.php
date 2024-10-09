<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\User\IndexRequestRequest;
use App\Http\Resources\RequestCollection;
use App\QueryFilters\RequestPaymentMethodFilter;
use App\QueryFilters\RequestStatusFilter;
use App\QueryFilters\RequestTypeFilter;
use App\QueryFilters\RequestVolumeFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexRequestController extends Controller
{
    public function __invoke(
        IndexRequestRequest $request
    ): JsonResponse
    {
        try {
            $requests = QueryBuilder::for(Auth::user()->requests()->withTrashed())
                ->allowedFilters([
                    AllowedFilter::custom('type', new RequestTypeFilter),
                    AllowedFilter::custom('status', new RequestStatusFilter),
                    AllowedFilter::custom('payment_method', new RequestPaymentMethodFilter),
                    AllowedFilter::custom('volume_from', new RequestVolumeFilter),
                    AllowedFilter::custom('volume_to', new RequestVolumeFilter),
                ])
                ->defaultSort(['-created_at', '-price'])
                ->allowedSorts('created_at', 'price')
                ->paginateWithDefault();

            $requests = new RequestCollection($requests);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.requests')]))
                ->data($requests)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
