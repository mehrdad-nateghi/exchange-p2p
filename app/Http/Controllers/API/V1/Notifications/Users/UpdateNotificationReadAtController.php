<?php

namespace App\Http\Controllers\API\V1\Notifications\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\User\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class UpdateNotificationReadAtController extends Controller
{
    public function __invoke(
        DatabaseNotification $notification,
    ): JsonResponse {
        try {
            $notification->markAsRead();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.notification')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
