<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/applicant/signin",
     *     summary="Sign in an applicant",
     *     tags={"Authentication"},
     *     operationId="applicantSignin",
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
    public function signin(SignInRequest $request){

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

    /**
     * @OA\Post(
     *     path="/api/applicant/signout",
     *     summary="Sign out an applicant",
     *     tags={"Authentication"},
     *     operationId="applicantSignout",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successfully signed out.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated.",
     *     ),
     * )
     */
    public function signout(Request $request){

        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully signed out.']);
    }
}
