<?php

namespace App\Http\Controllers\API\V1\Bids\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Bid\Users\StoreBidRequest;
use App\Http\Resources\BidResource;
use App\Services\API\V1\BidService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreBidController extends Controller
{
    public function __invoke(
        StoreBidRequest $request,
        BidService $bidService,
    ): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request = $bidService->create([
                'request_id' => $request->input('request_id'),
                'payment_method_id' => $request->input('payment_method_id'),
                'price' => $request->input('price'),
                'status' => $request->input('status'),
            ]);

            $resource = new BidResource($request);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.create_success', ['attribute' => trans('api-messages.bid')]))
                ->created()
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
