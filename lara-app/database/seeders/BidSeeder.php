<?php

namespace Database\Seeders;

use App\Models\Legacy\Bid;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    public function run()
    {
        Bid::factory(10)->create();
    }
}
