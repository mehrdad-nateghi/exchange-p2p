<?php

namespace App\Http\Controllers\API\V1\File\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\File\User\UpdateReceiptRequest;
use App\Http\Requests\API\V1\File\User\UploadReceiptRequest;
use App\Http\Requests\API\V1\Request\User\StoreRequestRequest;
use App\Http\Resources\FileResource;
use App\Http\Resources\RequestResource;
use App\Http\Resources\TradeStepResource;
use App\Models\File;
use App\Models\TradeStep;
use App\Services\API\V1\RequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateReceiptController extends Controller
{
    public function __invoke(
        File $file,
        UpdateReceiptRequest $request,
        //RequestService $requestService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $file->update([
                'status' => $request->input('status'),
            ]);

            $step = $file->fileable;

            $step->load('files.user');
            $resource =  new TradeStepResource($step);

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
