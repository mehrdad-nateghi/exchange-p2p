<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\VerifyCodeRequest;
use App\Services\API\V1\UserService;
use App\Services\API\V1\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifyCodeController extends Controller
{
    public function __invoke(
        VerifyCodeRequest $request,
        UserService $userService,
        VerificationCodeService $verificationCodeService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Find user
            $user = $userService->findBy('email', $validated['to']);

            // Log in the user after successful verification code
            $userService->authenticateUser($user);

            // Create a personal access token for the user
            $tokenData = $userService->createToken($user);
            // Create a refresh token and set in cookie
            $refreshToken = $userService->createRefreshToken($user);

            // Expire the code
            $verificationCode = $verificationCodeService->findLatest($validated['to'], $validated['via'], $validated['type']);
            $verificationCodeService->expireCode($verificationCode);

            // Prepare data
            $data = [
                'user' =>  $userService->createResource($user),
                'token' => $tokenData,
            ];

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.code_verified_successfully'))
                ->data($data)
                ->getApiResponseWithCookie($refreshToken['cookie']);
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
