<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\User\StoreRequestRequest;
use App\Http\Resources\RequestResource;
use App\Models\Request;
use App\Services\API\V1\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShowRequestController extends Controller
{
    public function __invoke(
        Request $request,
    ): JsonResponse {
        try {
            $resource =  new RequestResource($request);

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
