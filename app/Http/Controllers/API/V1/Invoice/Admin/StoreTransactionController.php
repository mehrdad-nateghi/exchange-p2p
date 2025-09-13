<?php

namespace App\Http\Controllers\API\V1\Invoice\Admin;

use App\Enums\TransactionProviderEnum;
use App\Enums\TransactionStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Invoice\Admin\StoreTransactionRequest;
use App\Http\Resources\Invoice\Admin\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreTransactionController extends Controller
{
    public function __invoke(
        StoreTransactionRequest $request,
        Invoice                 $invoice,
    )
    {
        try {
            DB::beginTransaction();

            $trackId = generateUniqueNumber('transactions', 'track_id');

            $transaction = $invoice->transactions()->create([
                'user_id' => $invoice->user_id,
                'track_id' => $trackId,
                'ref_id' => $request->ref_id,
                'amount' => $request->amount,
                'currency' => 'IRT',
                'provider' => TransactionProviderEnum::DIRECT_DEPOSIT->value,
                'status' => TransactionStatusEnum::COMPLETED->value
            ]);

            $resource = new InvoiceResource($invoice->fresh(['transactions']));

            // event(new TransferToSellerEvent($invoice->refresh()));

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.invoice')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
