<?php

namespace Database\Seeders;

use App\Models\LinkedMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinkedMethodSeeder extends Seeder
{
    public function run()
    {
        LinkedMethod::factory(10)->create();
    }
}
