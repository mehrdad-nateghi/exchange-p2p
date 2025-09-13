<?php

namespace Database\Seeders;

use App\Models\Legacy\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        Transaction::factory(10)->create();
    }
}
