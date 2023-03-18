<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Financial>
 */
class FinancialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'system_fee_a' => fake()->randomNumber(4),
            'system_fee_b' => fake()->randomNumber(4) ,
            'system_fee_c' => fake()->randomNumber(4) ,
            'system_fee_d' => fake()->randomNumber(4) ,
            'total_system_income' =>  fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'feasibility_band_percentage' => fake()->randomNumber(2) ,
            'updated_at' => fake()->dateTime()
        ];
    }
}
