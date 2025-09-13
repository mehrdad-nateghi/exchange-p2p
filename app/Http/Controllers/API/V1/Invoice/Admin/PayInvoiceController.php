<?php

namespace App\Http\Controllers\API\V1\Invoice\Admin;

use App\Enums\InvoiceStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Invoice\Admin\PayInvoiceRequest;
use App\Http\Resources\Invoice\Admin\InvoiceResource;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayInvoiceController extends Controller
{
    public function __invoke(
        PayInvoiceRequest $request,
        Invoice           $invoice,
    )
    {
        try {
            DB::beginTransaction();

            // invoice PAID
            $invoice->update([
                'status' => InvoiceStatusEnum::PAID->value,
            ]);

            $trade = $invoice->invoiceable;

            // trade step one DONE
            $trade->tradeSteps()->where('priority', 1)->first()->update([
                'status' => TradeStepsStatusEnum::DONE->value,
                'completed_at' => Carbon::now(),
            ]);

            // trade step two DOING
            $tradeStepTwo = $trade->tradeSteps()->where('priority', 2)->first();

            if ($tradeStepTwo) {
                $tradeStepTwo->update([
                    'status' => TradeStepsStatusEnum::DOING->value,
                    'expire_at' => Carbon::now()->addMinutes($tradeStepTwo->duration_minutes),
                ]);
            }

            $resource = new InvoiceResource($invoice->fresh(['transactions']));

            //todo: send notif
            //event(new PayTomanToSystemEvent($trade->refresh(), $invoice->refresh()));

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.invoice')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
