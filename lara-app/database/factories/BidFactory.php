<?php

namespace Database\Factories;

use App\Models\LinkedMethod;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bid>
 */
class BidFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => \App\Enums\BidTypeEnum::Sell ,
            'bid_rate' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'status' => \App\Enums\BidStatusEnum::Registered,
            'description' => fake()->text($maxNbChars = 50),
            'request_id' => Request::factory(),
            'applicant_id' => User::factory(),
            'target_account_id' => LinkedMethod::factory(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
