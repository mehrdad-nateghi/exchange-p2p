<?php

namespace App\Http\Controllers\Guest;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreSignUpRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
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
            return response(['message' => 'The verification code sent successfully. Please check your email to verify your account'], 200);
        }

        return response(['error' => 'Internal Server Error'], 500);
    }


    /**
     * @OA\Post(
     *     path="/api/user/signup/verify",
     *     summary="Veify the code and finalize signing up process",
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
     *              @OA\Property(property="user_id", type="string", description="An unique identifier for the user in the database.")
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

        return response(['message' => 'User signed up successfully', 'user_id' => $user->id], 200);
    }

}
