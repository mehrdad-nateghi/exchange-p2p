<?php

namespace App\Http\Controllers\API\V1\File\User;

use App\Enums\FileStatusEnum;
use App\Enums\InvoiceStatusEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\File\User\UpdateReceiptRequest;
use App\Http\Resources\TradeStepResource;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateReceiptController extends Controller
{
    public function __invoke(
        File                 $file,
        UpdateReceiptRequest $updateReceiptRequest,
        //RequestService $updateReceiptRequestService,
    ): JsonResponse
    {
        try {
            DB::beginTransaction();

            $status = $updateReceiptRequest->get('status');

            $file->update([
                'status' => $status,
            ]);

            $step = $file->fileable;
            $trade = $step->trade;

            // Accept
            if ($status === FileStatusEnum::ACCEPT_BY_BUYER->value) {
                // current step
                $currentStep = $trade->tradeSteps()->where('status', TradeStepsStatusEnum::DOING->value)->first();

                $currentStep->update([
                    'status' => TradeStepsStatusEnum::DONE->value,
                    'completed_at' => Carbon::now(),
                ]);

                // next step
                $nextStep = $trade->tradeSteps()->where('priority', $currentStep->priority + 1)->first();
                $nextStep->update([
                    'status' => TradeStepsStatusEnum::DOING->value,
                    'expire_at' => Carbon::now()->addMinutes($nextStep->duration_minutes),
                ]);
            }

            // Reject
            if ($status === FileStatusEnum::REJECT_BY_BUYER->value) {
                $trade->update([
                    'status' => TradeStatusEnum::SUSPEND->value
                ]);
            }

            $step->load('files.user');
            $resource = new TradeStepResource($step);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => 'receipt']))
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
