<?php

namespace App\Http\Controllers\API\V1\Invoice\User;

use App\Enums\TransactionStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Invoice\User\PayInvoiceRequest;
use App\Http\Resources\RequestResource;
use App\Models\Invoice;
use App\Services\API\V1\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Shetabit\Multipay\Invoice as ShetabitInvoice;
use Shetabit\Payment\Facade\Payment;

class PayInvoiceController extends Controller
{
    public function __invoke(
        PayInvoiceRequest $request,
        Invoice $invoice,
    ) {
        try {
            DB::beginTransaction();

            $paymentInvoice = (new ShetabitInvoice())->amount($invoice->total_payable_amount);

            $payment = Payment::purchase($paymentInvoice, function($driver,$refId) use($invoice){
                // todo-unique ref_id
                $invoice->transactions()->create([
                    'user_id' => Auth::id(),
                    'track_id' => $refId,
                    'amount' => $invoice->total_payable_amount,
                    'currency' => 'IRT',
                    'status' => TransactionStatusEnum::PENDING->value
                ]);
            });

            DB::commit();

            $paymentData = json_decode($payment->pay()->toJson(),true);
            return Redirect::away($paymentData['action']);
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
