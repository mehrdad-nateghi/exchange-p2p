<?php

namespace App\Http\Controllers\API\V1\Notifications\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Notification\User\UpdateNotificationsReadAllRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateNotificationsReadAllController extends Controller
{
    public function __invoke(UpdateNotificationsReadAllRequest $request): JsonResponse
    {
        try {
            Auth::user()->unreadNotifications()->update(['read_at' => now()]);

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.notifications')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
