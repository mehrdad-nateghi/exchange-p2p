<?php

namespace App\Http\Controllers\API\V1\Notifications\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\User\NotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class ShowNotificationController extends Controller
{
    public function __invoke(
        DatabaseNotification $notification,
    ): JsonResponse {
        try {
            $resource =  new NotificationResource($notification);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.notification')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
