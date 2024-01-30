<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="APIs for managing user authentication"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/admin/signin",
     *     summary="Sign in an admin",
     *     tags={"Authentication"},
     *     operationId="adminSignin",
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
    public function signIn(SignInRequest $request){

        $credentials = $request->only('email','password');

        if (auth()->attempt($credentials)) {
            $user = Auth::user();
            if ($user->role === UserRoleEnum::Admin) {
                $token = $user->createToken('AdminToken')->accessToken;
                return response(['token' => $token, 'user' => new UserResource($user)], 200);
            }
        }

        return response()->json(['message' =>'Unauthorized'], 401);
    }

        /**
     * @OA\Post(
     *     path="/api/admin/signout",
     *     summary="Sign out an admin",
     *     tags={"Authentication"},
     *     operationId="adminSignout",
     *     security={
     *           {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
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
}
