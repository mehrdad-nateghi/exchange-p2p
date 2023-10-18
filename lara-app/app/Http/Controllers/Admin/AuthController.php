<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/signin",
     *     summary="Admin sign-in",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Admin signed-in successfully.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request - Invalid input data",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function signIn(SignInRequest $request){

        $credentials = $request->only('email','password');

        if (auth()->attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === UserRoleEnum::Admin) {
                $token = $user->createToken('AdminToken')->accessToken;
                return response()->json(['token' => $token], 200);
            }
        }

        return response()->json(['message' =>'Unauthorized'], 401);
    }
}
