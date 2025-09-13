<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\RefreshTokenRequest;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefreshTokenController extends Controller
{
    public function __invoke(
        RefreshTokenRequest $request,
        UserService $userService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            foreach ($user->tokens as $token){
                $token->revoke();
            }

            $tokenData = $userService->createToken($user);
            $refreshToken = $userService->createRefreshToken($user);

            // Prepare data
            $data = [
                'token' => $tokenData,
            ];

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.token_refreshed_successfully'))
                ->data($data)
                ->getApiResponseWithCookie($refreshToken['cookie']);
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
