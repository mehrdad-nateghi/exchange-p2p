<?php

namespace App\Http\Controllers\API\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MeTestController extends Controller
{
    public function __invoke(): JsonResponse {
        try {
            $data = [
                'user' =>  $userService->createResource(User::query()->where('id',10)->first()),
            ];

            return apiResponse()
                ->message(trans('api-messages.user_info_retrieved_successfully'))
                ->data($data)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
