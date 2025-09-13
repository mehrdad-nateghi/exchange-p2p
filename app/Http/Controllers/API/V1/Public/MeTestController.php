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
            $totalStart = microtime(true);

            // Measure database query time
            $queryStart = microtime(true);
            $user = User::query()->where('id', 10)->first();
            $queryDuration = (microtime(true) - $queryStart) * 1000;
            Log::info("Database query took {$queryDuration} ms");

            // Measure UserService createResource time
            $resourceStart = microtime(true);
            $userData = $userService->createResource($user);
            $resourceDuration = (microtime(true) - $resourceStart) * 1000;
            Log::info("UserService createResource took {$resourceDuration} ms");

            $data = ['user' => $userData];

            // Measure apiResponse time
            $responseStart = microtime(true);
            $response = apiResponse()
                ->message(trans('api-messages.user_info_retrieved_successfully'))
                ->data($data)
                ->getApiResponse();
            $responseDuration = (microtime(true) - $responseStart) * 1000;
            Log::info("apiResponse took {$responseDuration} ms");

            $totalDuration = (microtime(true) - $totalStart) * 1000;
            Log::info("Total controller execution took {$totalDuration} ms");

            return $response;
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
