<?php

namespace Database\Seeders;

use App\Models\MethodAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MethodAttributeSeeder extends Seeder
{
    public function run()
    {
        MethodAttribute::factory(10)->create();
    }
}
