<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\RoleNameEnum;
use App\Enums\UserStatusEnum;
use App\Events\SignUpEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\SignUpRequest;
use App\Notifications\SignUpNotification;
use App\Services\API\V1\EmailNotificationService;
use App\Services\API\V1\UserService;
use App\Services\API\V1\VerificationCodeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignUpController extends Controller
{
    public function __invoke(
        SignUpRequest $request,
        UserService $userService,
        VerificationCodeService $verificationCodeService,
        EmailNotificationService $emailNotificationService
    ): JsonResponse {
        try {

            DB::beginTransaction();

            $validated = $request->validated();

            // Create user
            $user = $userService->create([
                'email' => $validated['to'],
                'email_verified_at' => Carbon::now(),
                'status' => UserStatusEnum::ACTIVE->value,
            ]);

            // Assign applicant role to user
            $userService->assignRoleToUser($user, RoleNameEnum::APPLICANT->value);

            // Log in the user after successful signup
            $userService->authenticateUser($user);

            // Create a personal access token for the user
            $tokenData = $userService->createToken($user);
            // Create a refresh token and set in cookie
            //$refreshToken = $userService->createRefreshToken($tokenData);

            // Dispatch Events
            SignUpEvent::dispatch($user);

            // Notifications
            $user->notify(new SignUpNotification());

            // Prepare data
            $data = [
                'user' =>  $userService->createResource($user),
                'token' => $tokenData,
            ];

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.user_signed_up_successfully'))
                ->data($data)
                ->getApiResponse();
                //->getApiResponseWithCookie($refreshToken['cookie']);
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
