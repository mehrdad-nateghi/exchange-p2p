<?php

namespace App\Http\Controllers\API\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MeTestController extends Controller
{
    public function __invoke(
        UserService $userService
    ): JsonResponse {
        try {
            $user = User::query()->where('id', 10)->first();
            $data = [
                'user' =>  $userService->createResource($user),
            ];

            $start = microtime(true);

            $response = apiResponse()
                ->message(trans('api-messages.user_info_retrieved_successfully'))
                ->data($data)
                ->getApiResponse();

            $duration = (microtime(true) - $start) * 1000;
            Log::info('apiResponse took ' . $duration . ' ms');

            return $response;
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
