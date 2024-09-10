<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Request\User\AddPaymentMethodRequest;
use App\Http\Requests\API\V1\Request\User\StoreRequestRequest;
use App\Http\Resources\RequestResource;
use App\Models\PaymentMethod;
use App\Models\Request;
use App\Services\API\V1\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddPaymentMethodOnRequestController extends Controller
{
    public function __invoke(
        AddPaymentMethodRequest $httpRequest,
        Request $request,
        PaymentMethod $paymentMethod,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $request->paymentMethods()->attach($paymentMethod);
            $resource =  new RequestResource($request->refresh());

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
