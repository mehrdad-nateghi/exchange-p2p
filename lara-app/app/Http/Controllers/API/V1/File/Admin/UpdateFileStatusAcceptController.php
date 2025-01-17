<?php

namespace App\Http\Controllers\API\V1\File\Admin;

use App\Enums\FileStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Enums\TradeStepsStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\File\Admin\UpdateFileStatusAcceptRequest;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateFileStatusAcceptController extends Controller
{
    public function __invoke(
        UpdateFileStatusAcceptRequest $request,
        File $file
    ): JsonResponse {
        try {

            DB::beginTransaction();

            $file->update([
                'status' => FileStatusEnum::ACCEPT_BY_ADMIN->value,
            ]);

            $trade = $file->fileable->trade;
            $currentStep = $trade->currentTradeStep();
            $nextStep = $trade->nextTradeStep();

            $currentStep->update([
                'status' => TradeStepsStatusEnum::DONE->value,
                'completed_at' => Carbon::now(),
            ]);

            $nextStep->update([
                'status' => TradeStepsStatusEnum::DOING->value,
                'expire_at' => Carbon::now()->addMinutes($nextStep->duration_minutes),
            ]);

            $trade->update([
                'status' => TradeStatusEnum::PROCESSING->value,
                'canceled_at' => null,
            ]);

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.file')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
