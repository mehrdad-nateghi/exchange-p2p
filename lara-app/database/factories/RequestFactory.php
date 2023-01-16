<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class RequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => '0' ,
            'trade_volume' => fake()->randomNumber($nbDigits = NULL, $strict = false),
            'lower_bound_feasibility_threshold' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'upper_bound_feasibility_threshold' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'acceptance_threshold' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'status' => '0' ,
            'is_removed' => '0' ,
            'created_at' => fake()->dateTime(),
            'applicant_id' => '1' ,
        ];
    }
}
