<?php

namespace App\Services\API\V1;

use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class UserService
{
    private User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function findBy($column,$value)
    {
        return $this->model->where($column,$value)->first();
    }

    public function createToken(User $user): array
    {
        $minutes = config('constants.access_token_expiration_time_per_minutes');
        $expirationMinutes = now()->addMinutes($minutes);
        $accessToken = $user->createToken('access_token',['*'], $expirationMinutes)->plainTextToken;

        /*$tokenResult = $user->createToken('access_token');
        $token = $tokenResult->token;
        $minutes = config('constants.access_token_expiration_time_per_minutes');
        $token->expires_at = Carbon::now()->addMinutes($minutes);
        $token->save();

        $accessToken = $tokenResult->accessToken;*/

        return [
            'access_token' => $accessToken,
            'type' => 'Bearer',
            'expires_at' => $expirationMinutes->toDateTimeString(), // todo-mn: must remvoe
        ];
    }

    public function createRefreshToken($tokenData): array
    {
       // $token = $user->tokens()->latest()->first()->token;

        $minutes = config('constants.refresh_token_expiration_time_per_minutes');

        //$refreshTokenCookie = Cookie::make('refresh_token',$token,$minutes,null,null,true,true,false,null);
        $refreshTokenCookie = Cookie::make('access_token',$tokenData['access_token'],$minutes,null,null,true,true,false,null);

        return [
            'cookie' => $refreshTokenCookie,
        ];


        /*$tokenResult = $user->createToken('refresh_token');
        $token = $tokenResult->token;
        $minutes = config('constants.refresh_token_expiration_time_per_minutes');
        $token->expires_at = Carbon::now()->addMinutes($minutes);
        $token->save();

        $refreshToken = 'Bearer ' . $tokenResult->accessToken;

        $refreshTokenCookie = Cookie::make('refresh_token',$refreshToken,$minutes,null,null,true,true,false,null);

        return [
            'cookie' => $refreshTokenCookie,
        ];*/
    }

    public function isEmailVerified(string $email): bool
    {
        $user = $this->model->where('email',$email)->first();
        return !empty($user) && $user->email_verified_at;
    }

    public function assignRoleToUser(User $user,$roleName): void
    {
        $user->assignRole($roleName);
    }

    public function authenticateUser(User $user): void
    {
        Auth::login($user);
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function createResource(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function setPassword(User $user,$password): bool
    {
        return $user->update([
            'password' => bcrypt($password)
        ]);
    }
}
