<?php

namespace App\Http\Controllers\API\V1\Bids\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\UpdateBidRequest;
use App\Http\Resources\BidResource;
use App\Models\Bid;
use App\Services\API\V1\BidService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBidController extends Controller
{
    public function __invoke(
        UpdateBidRequest $request,
        Bid $bid,
        BidService $bidService
    ): JsonResponse {

        try {
            DB::beginTransaction();

            $bid = $bidService->acceptBid($bid);

            $resource = new BidResource($bid);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.bid')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
