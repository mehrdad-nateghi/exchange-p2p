<?php

namespace App\Services\API\V1;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

    public function createToken(User $model): array
    {
        $accessToken = $model->createToken('access_token')->accessToken;
        $tokenType = 'Bearer';

        return [
            'access_token' => $accessToken,
            'type' => $tokenType,
        ];
    }

    public function isEmailVerified(string $email): bool
    {
        $user = User::where('email', $email)->first();
        return !empty($user) && $user->email_verified_at;
    }

    public function assignRoleToUser($user, $roleName): void
    {
        $user->assignRole($roleName);
    }

    public function authenticateUser($user): void
    {
        Auth::login($user);
    }

    public function createResource(User $model): UserData
    {
        return UserData::from($model);
    }
}
