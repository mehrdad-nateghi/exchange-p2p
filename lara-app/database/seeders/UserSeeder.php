<?php

namespace Database\Seeders;

use App\Enums\old\UserRoleEnum;
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
        // Create 10 Applicant user
        User::factory(10)
            ->create(['role'=>UserRoleEnum::Applicant]);

        // Create 10 Admin user
        User::factory(10)
            ->create(['role'=>UserRoleEnum::Admin]);

        // Create an Applicant by real email and password credentials for testing purpose
        $applicant = User::where('email','applicant@paylibero.com')->first();
        if(!$applicant) {
            User::factory(1)
            ->create(['role'=> UserRoleEnum::Applicant, 'email'=>'applicant@paylibero.com', 'password'=>Hash::make('123456')]);
        }

        // Create an Admin by real email and password credentials for testing purpose
        $admin = User::where('email','admin@paylibero.com')->first();
        if(!$admin) {
            User::factory(1)
            ->create(['role'=> UserRoleEnum::Admin, 'email'=>'admin@paylibero.com', 'password'=>Hash::make('123456')]);

        }

    }
}
