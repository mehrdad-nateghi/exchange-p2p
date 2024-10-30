<?php

namespace App\Http\Controllers\API\V1\Bids\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\Bids\Guest\BidCollection;
use App\Models\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class IndexBidsOfRequestController extends Controller
{
    public function __invoke(
        Request $request
    ): JsonResponse
    {
        try {
            $bids = $request->bids()
                ->with(['paymentMethod', 'request'])
                ->orderBy('created_at', 'desc')
                ->paginate();

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
