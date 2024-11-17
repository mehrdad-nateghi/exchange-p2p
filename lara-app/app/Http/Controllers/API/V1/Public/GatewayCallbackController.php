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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Payment\Facade\Payment;

class GatewayCallbackController extends Controller
{
    private $tradeUlId;

    public function __invoke(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $cancel = $request->integer('cancel');
            $success = $request->integer('success');
            $status = $request->integer('status');
            $trackId = $request->input('trackId');
            $refId = $request->input('orderId');

            $transaction = Transaction::where('track_id', $trackId)->first();
            $invoice = $transaction->transactionable;
            $trade = $invoice->invoiceable;
            $this->tradeUlId = $trade->ulid;

            if (($success !== 1 && $status !== 2) || $cancel == 1) {
                $transaction->update([
                    'status' => TransactionStatusEnum::FAILED,
                ]);

                DB::commit();

                throw new InvalidPaymentException(trans('api-messages.payment_failed'));
            }


            if ($transaction->status != TransactionStatusEnum::PENDING) {
                $transaction->update([
                    'status' => TransactionStatusEnum::FAILED,
                ]);

                DB::commit();

                throw new InvalidPaymentException("This transaction is already processed");
            }

            Payment::amount($transaction->amount)->transactionId($trackId)->verify();

            $transaction->update([
                'ref_id' => $refId,
                'status' => TransactionStatusEnum::COMPLETED,
            ]);

            // invoice PAID
            $invoice->update([
                'status' => InvoiceStatusEnum::PAID,
            ]);

            /*$trade->update([
                'status' => TradeStatusEnum::COMPLETED,
                'completed_at' => Carbon::now(),
            ]);*/

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

            return $this->redirectWithStatus(trans('api-messages.success'), trans('api-messages.payment_success'));
        } catch (InvalidPaymentException $exception) {
            DB::rollBack();

            $transaction->update([
                'status' => TransactionStatusEnum::FAILED,
            ]);

            Log::error($exception->getMessage());
            return $this->redirectWithStatus(trans('api-messages.failed'), trans('api-messages.payment_failed'));
        } catch (\Throwable $t) {
            DB::rollBack();

            $transaction->update([
                'status' => TransactionStatusEnum::FAILED,
            ]);

            Log::error($t);
            return $this->redirectWithStatus(trans('api-messages.error'), trans('api-messages.internal_server_error'));
        }
    }

    private function redirectWithStatus(string $status, string $message): RedirectResponse
    {
        $baseUrl = config('constants.frontend_url_after_payment');
        $redirectUrl = "{$baseUrl}/{$this->tradeUlId}?status={$status}&message=" . urlencode(strtolower($message));
        return redirect()->away($redirectUrl);
    }
}
