<?php

namespace App\Http\Controllers\API\V1\PaymentMethods\User;

use App\Enums\PaymentMethodTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\PaymentMethod\User\UpdatePaymentMethodRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePaymentMethodController extends Controller
{
    public function __invoke(
        UpdatePaymentMethodRequest $request,
        PaymentMethod $paymentMethod,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // RIAL ACCOUNT
            if($data['type'] === PaymentMethodTypeEnum::RIAL_BANK->value){
                $paymentMethod->paymentMethod()->update([
                    'holder_name' => $data['holder_name'],
                    'bank_name' => $data['bank_name'],
                    'card_number' => $data['card_number'],
                    'sheba' => $data['sheba'],
                    'account_no' => $data['account_no'],
                    'is_active' => $data['is_active'],
                ]);
            }

            // FOREIGN ACCOUNT
            if($data['type'] === PaymentMethodTypeEnum::FOREIGN_BANK->value){
                $paymentMethod->paymentMethod()->update([
                    'holder_name' => $data['holder_name'],
                    'bank_name' => $data['bank_name'],
                    'iban' => $data['iban'],
                    'bic' => $data['bic'],
                    'is_active' => $data['is_active'],
                ]);
            }

            // PAYPAL ACCOUNT
            if($data['type'] === PaymentMethodTypeEnum::PAYPAL->value){
                $paymentMethod->paymentMethod()->update([
                    'holder_name' => $data['holder_name'],
                    'email' => $data['email'],
                    'is_active' => $data['is_active'],
                ]);
            }

            $resource =  new PaymentMethodResource($paymentMethod->refresh());

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.payment_method')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}