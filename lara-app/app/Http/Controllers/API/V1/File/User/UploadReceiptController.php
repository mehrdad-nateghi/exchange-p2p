<?php

namespace App\Http\Controllers\API\V1\File\User;

use App\Enums\TradeStepsStatusEnum;
use App\Events\UploadReceiptEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\File\User\UploadReceiptRequest;
use App\Http\Resources\TradeStepResource;
use App\Models\TradeStep;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadReceiptController extends Controller
{
    public function __invoke(
        TradeStep $tradeStep,
        UploadReceiptRequest $request,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $file = $data['receipt'];

            $path = Storage::putFile('uploads', $file);

            $tradeStep->files()->create([
                'user_id' => Auth::id(),
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            $trade = $tradeStep->trade;

            // current step
            $tradeStep->update([
                'status' => TradeStepsStatusEnum::DONE,
                'completed_at' => Carbon::now(),
            ]);

            // next step
            $nextStep = $trade->tradeSteps()->where('priority', $tradeStep->priority + 1)->first();

            $nextStep->update([
                'status' => TradeStepsStatusEnum::DOING,
                'expire_at' => Carbon::now()->addMinutes($nextStep->duration_minutes),
            ]);

            $tradeStep->load('files.user');
            $resource =  new TradeStepResource($tradeStep);

            event(new UploadReceiptEvent($trade->refresh()));

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.upload_success', ['attribute' => 'receipt']))
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
