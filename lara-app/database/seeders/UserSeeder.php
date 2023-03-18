<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 10 Guset user
        User::factory(10)
            ->create(['type'=>UserTypeEnum::Guest]);

        // Create 10 Applicant user
        User::factory(10)
            ->create(['type'=>UserTypeEnum::Applicant]);
    }
}
