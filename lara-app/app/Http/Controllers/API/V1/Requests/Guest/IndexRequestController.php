<?php

namespace App\Http\Controllers\API\V1\Requests\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\Guest\IndexRequestRequest;
use App\Http\Resources\RequestCollection;
use App\Models\Request;
use App\QueryFilters\RequestStatusFilter;
use App\QueryFilters\RequestTypeFilter;
use Illuminate\Http\JsonResponse;
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
            $requests = QueryBuilder::for(Request::class)
                ->allowedFilters([
                    AllowedFilter::custom('type', new RequestTypeFilter),
                    AllowedFilter::custom('status', new RequestStatusFilter),
                ])
                ->defaultSort(['-created_at','-price'])
                ->allowedSorts('created_at','price')
                ->paginate();

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
