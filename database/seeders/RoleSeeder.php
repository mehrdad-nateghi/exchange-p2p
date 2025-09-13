<?php

namespace Database\Seeders;

use App\Enums\RoleNameEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Role::create([
            'name' => RoleNameEnum::ADMIN,
        ]);

        Role::create([
            'name' => RoleNameEnum::APPLICANT,
        ]);
    }
}
