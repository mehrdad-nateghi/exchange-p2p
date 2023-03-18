<?php

namespace Database\Seeders;

use App\Models\Financial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FinancialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $recordCount = Financial::count();

        if($recordCount === 0){
            Financial::factory(1)->create();
        } else{
            Financial::first()->delete();
            Financial::factory(1)->create();
        }
    }
}
