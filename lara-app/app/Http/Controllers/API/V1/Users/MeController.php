<?php

namespace App\Http\Controllers\API\V1\Users;

use App\Http\Controllers\Controller;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MeController extends Controller
{
    public function __invoke(
        UserService $userService
    ): JsonResponse {
        try {
            $data = [
                'user' =>  $userService->createResource(Auth::user()),
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