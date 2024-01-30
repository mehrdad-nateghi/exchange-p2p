<?php

namespace App\Http\Controllers\Applicant;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\SignInRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="An encrypted token for authorizing the signed in user."),
     *             @OA\Property(property="user", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="A unique identifier for the user in the dataset."),
     *                 @OA\Property(property="role", type="integer", description="A field indicates user role. 0: Applicant, 1: Admin."),
     *                 @OA\Property(property="first_name", type="string", description="A field indicates user's first name."),
     *                 @OA\Property(property="last_name", type="string", description="A field indicates user's last name."),
     *                 @OA\Property(property="email", type="string", description="A field indicates user's email."),
     *                 @OA\Property(property="status", type="integer", description="A field indicates user's status. 0: Deactive, 1: Active"),
     *                 @OA\Property(property="is_email_verified", type="boolean", description="A field indicates whther user's email is verified or not. true: is_verified, false: is_not_verified"),
     *             )),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request",
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
                return response(['token' => $token, 'user' => new UserResource($user)], 200);
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
     *         description="Successful operation.",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized.",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function signout(Request $request){

        $request->user()->token()->revoke();

        return response()->json(['message' => 'Successfully signed out.']);
    }

    /**
     * @OA\Post(
     *     path="/api/applicant/set-password",
     *     summary="Set password to the applicant account by authenticated applicant",
     *     tags={"Authentication"},
     *     operationId="setPasswordToApplicantAccountByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="password", type="string", description="The password must contain at least 8 digits, one lowercase letter, one uppercase letter, one digit, and one special character"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request."),
     *      )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function setPassword(SetPasswordRequest $request){

        $applicant = Auth::user();

        $validated_credentials = $request->validated();
        $password = $validated_credentials['password'];

        if($applicant->password) {
            return response(['message' => 'The password is already set for the applicant.'], 422);
        }

        $update_password = $applicant->updatePassword($password);

        if($update_password) {
            return response(['message' => 'The password set successfully'], 200);
        }

        return response(['error' => 'Internal server error.'], 500);
    }

    /**
     * @OA\Post(
     *     path="/api/applicant/reset-password",
     *     summary="Reset the password of the applicant account by authenticated applicant",
     *     tags={"Authentication"},
     *     operationId="resetPasswordOfApplicantAccountByApplicant",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="password", type="string", description="The new password must contain at least 8 digits, one lowercase letter, one uppercase letter, one digit, and one special character"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request."),
     *      )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable request",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *     )
     * )
     */
    public function resetPassword(SetPasswordRequest $request){

        $applicant = Auth::user();

        $validated_credentials = $request->validated();
        $password = $validated_credentials['password'];

        $update_password = $applicant->updatePassword($password);

        if($update_password) {
            return response(['message' => 'The password reset successfully'], 200);
        }

        return response(['error' => 'Internal server error.'], 500);
    }
}
