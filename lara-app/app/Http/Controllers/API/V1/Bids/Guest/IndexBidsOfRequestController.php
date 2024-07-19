<?php

namespace App\Http\Controllers\API\V1\Bids\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\IndexBidRequest;
use App\Http\Requests\API\V1\Request\User\IndexRequestRequest;
use App\Http\Resources\BidCollection;
use App\Http\Resources\RequestCollection;
use App\Models\Request;
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

class IndexBidsOfRequestController extends Controller
{
    public function __invoke(
        Request $request
    ): JsonResponse
    {
        try {
            $bids = $request->bids()->orderBy('created_at', 'desc')->paginate();
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
