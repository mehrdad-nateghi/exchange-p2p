<?php

namespace App\Http\Controllers\API\V1\PaymentMethods\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodCollection;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class IndexPaymentMethodController extends Controller
{
    public function __invoke(
    ): JsonResponse
    {
        try {
            $paymentMethods = new PaymentMethodCollection(PaymentMethod::paginate());

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.payment_methods')]))
                ->data($paymentMethods)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}