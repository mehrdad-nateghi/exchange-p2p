<?php

namespace Database\Seeders;

use App\Models\DepositReason;
use Illuminate\Database\Seeder;

class DepositReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DepositReason::factory()->count(8)->create();
    }
}
