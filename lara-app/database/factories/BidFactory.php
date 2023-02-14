<?php

namespace Database\Factories;

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
            'request_id' => '1' ,
            'applicant_id' => '1' ,
            'created_at' => fake()->dateTime(),
        ];
    }
}
