<?php

namespace App\Http\Controllers\API\V1\Requests\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\Requests\Guest\RequestResource;
use App\Models\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShowRequestController extends Controller
{
    public function __invoke(
        Request $request,
    ): JsonResponse {
        try {
            $resource =  new RequestResource($request->load(['paymentMethods.paymentMethod','latestTradeWithTrashed','user']));

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.request')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
