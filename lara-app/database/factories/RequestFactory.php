<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'support_id' => config('constants.SupportId_Prefixes.Request_Pr'). Str::uuid(),
            'type' => \App\Enums\RequestTypeEnum::Sell ,
            'trade_volume' => fake()->randomNumber($nbDigits = NULL, $strict = false),
            'lower_bound_feasibility_threshold' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'upper_bound_feasibility_threshold' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'acceptance_threshold' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'request_rate' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'status' => \App\Enums\RequestStatusEnum::Pending ,
            'description' => fake()->text($maxNbChars = 50),
            'is_removed' => '0',
            'applicant_id' => User::factory(),
        ];
    }
}
