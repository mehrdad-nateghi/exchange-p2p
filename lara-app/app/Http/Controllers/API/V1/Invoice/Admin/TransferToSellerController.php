<?php

namespace App\Http\Controllers\API\V1\Invoice\Admin;

use App\Enums\InvoiceStatusEnum;
use App\Enums\PaymentMethodTypeEnum;
use App\Enums\TransactionProviderEnum;
use App\Enums\TransactionStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Invoice\Admin\TransferToSellerRequest;
use App\Http\Requests\API\V1\Invoice\User\PayInvoiceRequest;
use App\Http\Resources\Invoice\Admin\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Shetabit\Multipay\Invoice as ShetabitInvoice;
use Shetabit\Payment\Facade\Payment;

class TransferToSellerController extends Controller
{
    public function __invoke(
        TransferToSellerRequest $request,
        Invoice                 $invoice,
    )
    {
        try {
            DB::beginTransaction();

            $clientId = config('finnotech.client_id');
            $token = config('finnotech.authorization_code_token');
            $baseUrl = config('finnotech.base_url');
            $endpoint = "/oak/v2/clients/{$clientId}/transferTo";

            $trackId = generateUniqueNumber('transactions', 'track_id');
            $amount = $invoice->amount - $invoice->fee;

            $transaction = $invoice->transactions()->create([
                'user_id' => $invoice->user_id,
                'track_id' => $trackId,
                'amount' => $amount,
                'currency' => 'IRT',
                'provider' => TransactionProviderEnum::FINNOTECH->value,
                'status' => TransactionStatusEnum::PENDING->value
            ]);

            $user = $invoice->user;  // access relationship once
            $rialBankAccount = $invoice->invoiceable->paymentMethods()->where('payment_methods.type', PaymentMethodTypeEnum::RIAL_BANK->value)->first()->paymentMethod;

            $queryParams = [
                'trackId' => $trackId,
            ];

            $bodyParams = [
                'amount' => $amount,
                'destinationFirstname' => $user->first_name,
                'destinationLastname' => $user->last_name,
                'destinationNumber' => $rialBankAccount->iban,
                'description' => $invoice->number,
                'reasonDescription' => $invoice->number
            ];

            $url = $baseUrl . $endpoint . '?' . http_build_query($queryParams);

            $response = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($url, $bodyParams);

            $data = $response->json();

            if ($response->successful() && $data['status'] === 'DONE') {
                $invoice->update([
                    'status' => InvoiceStatusEnum::PAID->value
                ]);

                $transaction->update([
                    'status' => TransactionStatusEnum::COMPLETED->value,
                    'ref_id' => $data['result']['refCode'],
                    'metadata' => $data
                ]);

                $resource = new InvoiceResource($invoice->refresh());

                DB::commit();

                return apiResponse()
                    ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.invoice')]))
                    ->data($resource)
                    ->getApiResponse();
            } else {
                $transaction->update([
                    'status' => TransactionStatusEnum::FAILED->value,
                    'metadata' => $data
                ]);

                $resource = new InvoiceResource($invoice->refresh());

                DB::commit();

                return apiResponse()
                    ->failed()
                    ->message(trans('api-messages.request_failed', ['attribute' => trans('api-messages.pay_invoice')]))
                    ->data($resource)
                    ->getApiResponse();
            }
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
