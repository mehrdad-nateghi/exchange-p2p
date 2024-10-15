<?php

namespace App\Http\Controllers\API\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Admin\UserStatsResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UserStatsController extends Controller
{
    public function __invoke(
        User $user
    ): JsonResponse {
        try {
            $resource = new UserStatsResource($user);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.user_stats')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
