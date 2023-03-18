<?php

namespace Database\Seeders;

use App\Models\TransactionMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionMethodSeeder extends Seeder
{
    public function run()
    {
        TransactionMethod::factory(10)->create();
    }
}
