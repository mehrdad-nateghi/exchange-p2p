<?php

namespace Database\Seeders;

use App\Models\FrequentQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrequentQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FrequentQuestion::factory(10)->create();
    }
}
