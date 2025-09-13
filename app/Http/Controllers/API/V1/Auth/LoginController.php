<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\RoleNameEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Auth\LoginRequest;
use App\Services\API\V1\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __invoke(
        LoginRequest $request,
        UserService $userService,
    ): JsonResponse {
        //dd($request->all());
        try {
            DB::beginTransaction();

            //$credentials = $request->only('email', 'password');
            $credentials = $request->getCredentials();
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                // Check if user has admin role, use admin_session cookie
                /*if ($user->hasRole(RoleNameEnum::ADMIN->value)) {
                    // Save current session data
                    $sessionData = session()->all();

                    // Clear current session
                    session()->flush();

                    // Change session name for admin
                    config(['session.cookie' => 'admin_session']);

                    // Start new session with admin name
                    $request->session()->setName('admin_session');

                    // Regenerate session
                    $request->session()->regenerate();

                    // Restore session data
                    foreach ($sessionData as $key => $value) {
                        session()->put($key, $value);
                    }
                }*/

                $tokenData = $userService->createToken($user);
                //$refreshToken = $userService->createRefreshToken($user);

                $data = [
                    'user' =>  $userService->createResource($user),
                    'token' => $tokenData,
                ];

                DB::commit();

                return apiResponse()
                    ->message(trans('api-messages.user_logged_in_successfully'))
                    ->data($data)
                    ->getApiResponse();
                    //->getApiResponseWithCookie($refreshToken['cookie']);
            }

            DB::rollBack();

            return apiResponse()
                ->failed()
                ->message(trans('api-messages.invalid_credentials'))
                ->unAuthorized()
                ->getApiResponse();

        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
