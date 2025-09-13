<?php

namespace App\Http\Controllers\API\V1\Test;

use App\Http\Controllers\Controller;
use App\Notifications\TestNotification;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SendTestNotificationController extends Controller
{
    public function __invoke(): JsonResponse {
        try {
            auth()->user()->notify(new TestNotification());

            return apiResponse()
                ->message('Test notification sent')
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
