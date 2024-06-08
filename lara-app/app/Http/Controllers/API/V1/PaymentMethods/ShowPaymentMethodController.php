<?php

namespace App\Http\Controllers\API\V1\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ShowPaymentMethodController extends Controller
{
    public function __invoke(
        User $user
    ): JsonResponse {
        try {
            $paymentMethods = new PaymentMethodCollection($user->paymentMethods()->paginate());

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.user_payment_methods')]))
                ->data($paymentMethods)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}