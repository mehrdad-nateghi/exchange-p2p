<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\User\StoreRequestRequest;
use App\Http\Resources\RequestResource;
use App\Services\API\V1\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreRequestController extends Controller
{
    public function __invoke(
        StoreRequestRequest $request,
        RequestService $requestService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $user = Auth::user();

            $request = $requestService->create($user,$data);
            $requestService->attachPaymentMethod($request,$data['payment_methods']);

            $resource =  new RequestResource($request);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.create_success', ['attribute' => trans('api-messages.request')]))
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
