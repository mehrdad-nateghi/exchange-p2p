<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/applicant/signin",
     *     summary="Applicant sign-in",
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
     *         description="Applicant signed-in successfully.",
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
            if ($user->role === UserRoleEnum::Applicant) {
                $token = $user->createToken('ApplicantToken')->accessToken;
                return response()->json(['token' => $token], 200);
            }
        }

        return response()->json(['message' =>'Unauthorized'], 401);
    }
}
