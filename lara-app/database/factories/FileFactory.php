<?php

namespace Database\Factories;

use App\Models\Legacy\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Legacy\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'url' => fake()->text($maxNbChars = 50) ,
            'alt' => fake()->word() ,
            'type' => fake()->word() ,
            'transaction_id' => Transaction::factory()
        ];
    }
}
