<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class LogOutController extends Controller
{
    public function __invoke(
        UserService $userService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            /*foreach ($user->tokens as $token){
                $token->revoke();
            }*/

            $userService->logout($user);

            //Auth::logout();

            DB::commit();

            //$cookie = cookie()->forget('refresh_token');

            return apiResponse()
                ->success()
                ->message(trans('api-messages.logout_successful'))
                ->getApiResponse();
                //->withCookie($cookie);

        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
