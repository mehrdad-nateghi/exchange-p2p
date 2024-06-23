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
        $tokenResult = $user->createToken('access_token');
        $token = $tokenResult->token;
        $minutes = config('constants.access_token_expiration_time_per_minutes');
        $token->expires_at = Carbon::now()->addMinutes($minutes);
        $token->save();

        $accessToken = $tokenResult->accessToken;

        return [
            'access_token' => $accessToken,
            'type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ];
    }

    public function createRefreshToken(User $user): array
    {
        $tokenResult = $user->createToken('refresh_token');
        $token = $tokenResult->token;
        $minutes = config('constants.refresh_token_expiration_time_per_minutes');
        $token->expires_at = Carbon::now()->addMinutes($minutes);
        $token->save();

        $refreshToken = $tokenResult->accessToken;

        $refreshTokenCookie = Cookie::make('refresh_token',$refreshToken,$minutes,null,null,true,true,false,null);

        return [
            'cookie' => $refreshTokenCookie,
        ];
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
