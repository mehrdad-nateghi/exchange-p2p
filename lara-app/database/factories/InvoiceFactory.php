<?php

namespace Database\Factories;

use App\Models\LinkedMethod;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Request>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'trade_stage' => \App\Enums\InvoiceTypeEnum::RialPending,
            'trade_net_value' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'trade_fee' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'trade_gross_value' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'status' => \App\Enums\InvoiceStatusEnum::Open,
            'applicant_id' => User::factory(),
            'trade_id' => Trade::factory(),
            'target_account_id' => LinkedMethod::factory(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
