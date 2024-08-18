<?php

namespace App\Http\Controllers\API\V1\Public;

use App\Enums\InvoiceStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Enums\TransactionStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\TradeResource;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;

class GatewayCallbackController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $success = $request->integer('success');
            $status = $request->integer('status');
            $trackId = $request->input('trackId');
            $refId = $request->input('orderId');

            if ($success !== 1 && $status !== 2) {
                throw new \Exception(trans('api-messages.payment_failed'));
            }

            $transaction = Transaction::where('track_id', $trackId)->first();

            if ($transaction->status != TransactionStatusEnum::PENDING) {
                throw new \Exception("This transaction is already processed");
            }

            Payment::amount($transaction->amount)->transactionId($trackId)->verify();

            $transaction->update([
                'ref_id' => $refId,
                'status' => TransactionStatusEnum::COMPLETED,
            ]);

            // invoice PAID
            $invoice = $transaction->transactionable;
            $invoice->update([
                'status' => InvoiceStatusEnum::PAID,
            ]);

            // trade COMPLETED
            $trade = $invoice->invoiceable;
            $trade->update([
                'status' => TradeStatusEnum::COMPLETED,
                'completed_at' => Carbon::now(),
            ]);

            // trade step one DONE
            $trade->tradeSteps()->where('priority', 1)->first()->update([
                'status' => TradeStepsStatusEnum::DONE,
                'completed_at' => Carbon::now(),
            ]);

            // trade step two DOING
            $tradeStepTwo = $trade->tradeSteps()->where('priority', 2)->first();

            if ($tradeStepTwo) {
                $tradeStepTwo->update([
                    'status' => TradeStepsStatusEnum::DOING,
                    'expire_at' => Carbon::now()->addMinutes($tradeStepTwo->duration_minutes),
                ]);
            }

            DB::commit();

            $resource = new TradeResource($trade->refresh()->load(['tradeSteps', 'invoices']));

            return apiResponse()
                ->message(trans('api-messages.payment_success'))
                ->data($resource)
                ->getApiResponse();
        } catch (InvalidPaymentException $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            return apiResponse()
                ->failed()
                ->paymentRequired()
                ->message(trans('api-messages.payment_failed'))
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
