<?php

namespace App\Http\Controllers\API\V1\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodCollection;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeletePaymentMethodController extends Controller
{
    public function __invoke(
        PaymentMethod $paymentMethod,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $paymentMethod->paymentMethod()->delete();
            $paymentMethod->delete();

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.delete_success', ['attribute' => trans('api-messages.user_payment_method')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}