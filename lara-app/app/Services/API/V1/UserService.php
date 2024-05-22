<?php

namespace App\Services\API\V1;

use App\Data\UserData;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function findBy($column, $value)
    {
        return $this->model->where($column,$value)->first();
    }

    public function createToken(User $user): array
    {
        $accessToken = $user->createToken('access_token')->accessToken;
        $tokenType = 'Bearer';

        return [
            'access_token' => $accessToken,
            'type' => $tokenType,
        ];
    }

    public function isEmailVerified(string $email): bool
    {
        $user = $this->model->where('email', $email)->first();
        return !empty($user) && $user->email_verified_at;
    }

    public function assignRoleToUser(User $user, $roleName): void
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

    public function setPassword(User $user, $password): bool
    {
        return $user->update([
            'password' => bcrypt($password)
        ]);
    }
}
