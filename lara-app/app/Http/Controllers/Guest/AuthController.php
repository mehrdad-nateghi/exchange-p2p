<?php

namespace App\Http\Controllers\Guest;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreForgetPasswordRequest;
use App\Http\Requests\PreResetPasswordRequest;
use App\Http\Requests\PreSignUpRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{

    /*
     * Create and send verification code via email
     */
    public function sendVerificationCodeByEmail($email){

        // Generate an unique code
        $code = random_int(100000, 999999);
        $emailVerification = EmailVerification::Create(
            [
                'email' => $email,
                'code' => Crypt::encryptString($code),
                'expired_at' => Carbon::now()->addMinutes(config('constants.Verification_Code_Expiration_Per_Minutes'))
            ]
        );

        if($emailVerification) {
            $emailSentStatus = $emailVerification->sendCode();

            if($emailSentStatus) {
                return true;
            }
        }

        return false;
    }

    /**
     * @OA\Post(
     *     path="/api/user/signup/send-code",
     *     summary="Send verification code by email during the user signing up process",
     *     tags={"Authentication"},
     *     operationId="userPreSignup",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request.")
     *      )
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
    protected function preSignup(PreSignUpRequest $request)
    {
        $validated_credentials = $request->validated();

        $user = User::where('email', $validated_credentials['email'])->first();
        if($user instanceof User) {
            return response(['message' => 'A user has already registered with the input email address.'], 422);
        }

        $emailVerificationCode = $this->sendVerificationCodeByEmail($validated_credentials['email']);

        if($emailVerificationCode) {
            return response(['message' => 'The verification code sent successfully.'], 200);
        }

        return response(['error' => 'Internal Server Error'], 500);
    }


    /**
     * @OA\Post(
     *     path="/api/user/signup/verify",
     *     summary="Veify the code,  finalize signing up process and then sign in the user accordingly",
     *     tags={"Authentication"},
     *     operationId="userSignup",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="code", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request."),
     *              @OA\Property(property="token", type="string", description="A token generated for the user as a consequence of signing in process.")
     *      )
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
    public function signUp(SignUpRequest $request)
    {
        $validated_credentials = $request->validated();

        $email = $validated_credentials['email'];
        $code = $validated_credentials['code'];

        $user = User::where('email', $email)->first();
        if($user instanceof User && $user->email_verified_at) {
            return response(['message' => 'A user has already registered with the input email address.'], 422);
        }

        $verificationInstance = EmailVerification::where('email', $email)
        ->latest('created_at') // Order by created_at column in descending order to ensure the latest generated verification code for the input email is considering
        ->first();

        if (!$verificationInstance || !$verificationInstance->isValid($code)) {
            return response(['error' => 'Invalid verification code.'], 422);
        }

        $user = User::create([
            'email' => $email,
            'role' => UserRoleEnum::Applicant,
            'email_verified_at' => Carbon::now(),
        ]);

        if(!$user) {
            return response(['error' => 'Internal server error.'], 500);
        }

        $user->refresh();

        // Log in the user after successful signup
        Auth::login($user);

        // Create a personal access token for the user
        $token = $user->createToken('ApplicantToken')->accessToken;

        return response(['message' => 'User signed up and signed in successfully', 'token' => $token], 200);

    }


    /**
     * @OA\Post(
     *     path="/api/user/reset-password/send-code",
     *     summary="Send verification code by email during the user password reseting process",
     *     tags={"Authentication"},
     *     operationId="userPreResetPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", description="A descriptive attribute indicating the result of request.")
     *      )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
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
    public function preResetPassword(PreResetPasswordRequest $request){

        $validated_credentials = $request->validated();
        $email = $validated_credentials['email'];

        $user = User::where('email', $email)->first();
        if(!$user) {
            return response(['message' => 'User not found.'], 404);
        }

        $emailVerificationCode = $this->sendVerificationCodeByEmail($email);

        if($emailVerificationCode) {
            return response(['message' => 'The verification code sent successfully.'], 200);
        }

        return response(['error' => 'Internal Server Error'], 500);
    }

     /**
     * @OA\Post(
     *     path="/api/user/reset-password/set-new-password",
     *     summary="Verify the input code as well as setting new password during the user password reseting process",
     *     tags={"Authentication"},
     *     operationId="userResetPassword",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="code", type="string"),
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
     *         response=404,
     *         description="User not found",
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
    public function resetPassword(ResetPasswordRequest $request)
    {
        $validated_credentials = $request->validated();

        $email = $validated_credentials['email'];
        $code = $validated_credentials['code'];
        $password = $validated_credentials['password'];

        $verificationInstance = EmailVerification::where('email', $email)
        ->latest('created_at') // Order by created_at column in descending order to ensure the latest generated verification code for the input email is considering
        ->first();

        if (!$verificationInstance || !$verificationInstance->isValid($code)) {
            return response(['error' => 'Invalid verification code.'], 422);
        }

        $user = User::where('email', $email)->first();

        if(!$user) {
            return response(['message' => 'User not found.'], 404);
        }

        $update_password = $user->updatePassword($password);

        if($update_password) {
            return response(['message' => 'The password reset successfully'], 200);
        }

        return response(['error' => 'Internal server error.'], 500);
    }
}
