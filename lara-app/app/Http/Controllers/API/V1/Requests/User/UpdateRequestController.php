<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\User\UpdateRequestRequest;
use App\Http\Resources\RequestResource;
use App\Models\Request;
use App\Services\API\V1\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRequestController extends Controller
{
    public function __invoke(
        UpdateRequestRequest $updateRequest,
        Request $request,
        RequestService $requestService
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $data = $updateRequest->validated();

            $requestService->update($request, $data);

            $resource = new RequestResource($request->refresh());

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.request')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
