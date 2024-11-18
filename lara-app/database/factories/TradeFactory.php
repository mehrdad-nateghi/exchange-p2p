<?php

namespace Database\Factories;

use App\Models\Legacy\Bid;
use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class TradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'support_id' => config('constants.SupportId_Prefixes.Trade_Pr'). Str::uuid(),
            'trade_fee' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'status' => \App\Enums\Legacy\TradeStatusEnum::RialPending,
            'request_id' => Request::factory(),
            'bid_id' => Bid::factory(),
            'created_at' => fake()->dateTime()
        ];
    }
}
