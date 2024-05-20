<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class LoginController extends Controller
{
    public function __invoke(
        LoginRequest $request,
        UserService $userService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $tokenData = $userService->createToken($user);

                $data = [
                    'user' =>  $userService->createResource($user),
                    'token' => $tokenData,
                ];

                DB::commit();

                return apiResponse()
                    ->message(trans('api-message.user_logged_in_successfully'))
                    ->data($data)
                    ->getApiResponse();
            }

            DB::rollBack();

            return apiResponse()
                ->failed()
                ->message(trans('api-message.invalid_credentials'))
                ->unAuthorized()
                ->getApiResponse();

        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
