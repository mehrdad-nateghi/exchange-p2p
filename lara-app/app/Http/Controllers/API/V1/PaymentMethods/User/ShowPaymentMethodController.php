<?php

namespace App\Http\Controllers\API\V1\PaymentMethods\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShowPaymentMethodController extends Controller
{
    public function __invoke(
        PaymentMethod $paymentMethod,
    ): JsonResponse {
        try {
            $resource =  new PaymentMethodResource($paymentMethod);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.user_payment_method')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}