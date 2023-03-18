<?php

namespace Database\Seeders;

use App\Models\UserVerify;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserVerifySeeder extends Seeder
{
    public function run()
    {
        UserVerify::factory(10)->create();
    }
}
