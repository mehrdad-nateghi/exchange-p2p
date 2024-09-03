<?php

namespace Database\Seeders;

use App\Enums\Legacy\UserRoleEnum;
use App\Enums\RoleNameEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createUser('bidder', RoleNameEnum::APPLICANT);
        $this->createUser('requester', RoleNameEnum::APPLICANT);
        $this->createUser('admin', RoleNameEnum::ADMIN);
    }

    /**
     * Create a user with given type and role.
     *
     * @param string $type
     * @param RoleNameEnum $role
     * @return User
     */
    private function createUser(string $type, RoleNameEnum $role): User
    {
        return User::factory()->create([
            'first_name' => "$type first name",
            'last_name' => "$type last name",
            'email' => "$type@paylibero.com",
            'password' => Hash::make('123456'),
        ])->assignRole($role->value);
    }
}
