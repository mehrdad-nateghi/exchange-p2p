<?php

namespace App\Http\Controllers\API\V1\Users\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\User\User\ChangePasswordRequest;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ChangePasswordController extends Controller
{
    public function __invoke(
        ChangePasswordRequest $request,
        UserService $userService,
    ): JsonResponse {
        try {
            $user = Auth::user();

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            $userService->logout($request,$user);

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.user')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
