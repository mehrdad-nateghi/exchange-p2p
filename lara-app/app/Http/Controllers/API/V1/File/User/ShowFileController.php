<?php

namespace App\Http\Controllers\API\V1\File\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShowFileController extends Controller
{
    public function __invoke(
        File $file,
    ): JsonResponse {
        try {
            $resource =  new FileResource($file);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => 'file']))
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
