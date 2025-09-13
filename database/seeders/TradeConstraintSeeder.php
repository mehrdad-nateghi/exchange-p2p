<?php

namespace Database\Seeders;

use App\Models\TradeConstraint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TradeConstraintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $recordCount = TradeConstraint::count();

        if($recordCount === 0){
            TradeConstraint::factory(1)->create();
        } else{
            TradeConstraint::first()->delete();
            TradeConstraint::factory(1)->create();
        }
    }
}
