<?php

namespace App\Http\Controllers\API\V1\PaymentMethods;

use App\Enums\PaymentMethodTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\PaymentMethod\StorePaymentMethodRequest;
use App\Models\PaymentMethod;
use App\Services\API\V1\ForeignBankAccountService;
use App\Services\API\V1\PaymentMethodService;
use App\Services\API\V1\PaypalAccountService;
use App\Services\API\V1\RialBankAccountService;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StorePaymentMethodController extends Controller
{
    public function __invoke(
        StorePaymentMethodRequest $request,
        PaymentMethodService $paymentMethodService,
        RialBankAccountService $rialBankAccountService,
        ForeignBankAccountService $foreignBankAccountService,
        PaypalAccountService $paypalAccountService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $resource = [];

            // RIAL ACCOUNT
            if($data['type'] == PaymentMethodTypeEnum::RIAL_BANK->value){
                $rialBankAccount = $rialBankAccountService->create([
                    'holder_name' => $data['holder_name'],
                    'bank_name' => $data['bank_name'],
                    'card_number' => $data['card_number'],
                    'sheba' => $data['sheba'],
                    'account_no' => $data['account_no'],
                    'is_active' => $data['is_active'],
                ]);

                $rialBankAccountService->createPaymentMethod($rialBankAccount,[
                    'user_id' => Auth::id(),
                    'type' => $data['type']
                ]);

                $resource = $rialBankAccountService->createResource($rialBankAccount);
            }

            // FOREIGN ACCOUNT
            if($data['type'] == PaymentMethodTypeEnum::FOREIGN_BANK->value){
                $foreignBankAccount = $foreignBankAccountService->create([
                    'holder_name' => $data['holder_name'],
                    'bank_name' => $data['bank_name'],
                    'iban' => $data['iban'],
                    'bic' => $data['bic'],
                    'is_active' => $data['is_active'],
                ]);

                $foreignBankAccountService->createPaymentMethod($foreignBankAccount,[
                    'user_id' => Auth::id(),
                    'type' => $data['type']
                ]);

                $resource = $foreignBankAccountService->createResource($foreignBankAccount);
            }

            // PAYPAL ACCOUNT
            if($data['type'] == PaymentMethodTypeEnum::PAYPAL->value){
                $paypalAccount = $paypalAccountService->create([
                    'holder_name' => $data['holder_name'],
                    'email' => $data['email'],
                    'is_active' => $data['is_active'],
                ]);

                $paypalAccountService->createPaymentMethod($paypalAccount,[
                    'user_id' => Auth::id(),
                    'type' => $data['type']
                ]);

                $resource = $paypalAccountService->createResource($paypalAccount);
            }

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.create_success', ['attribute' => 'payment method']))
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