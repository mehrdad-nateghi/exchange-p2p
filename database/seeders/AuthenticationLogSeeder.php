<?php

namespace Database\Seeders;

use App\Models\AuthenticationLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AuthenticationLogSeeder extends Seeder
{
    public function run()
    {
        AuthenticationLog::factory(10)->create();
    }
}
