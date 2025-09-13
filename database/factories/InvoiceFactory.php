<?php

namespace Database\Factories;

use App\Models\Trade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'support_id' => config('constants.SupportId_Prefixes.Invoice_Pr'). Str::uuid(),
            'trade_net_value' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'trade_fee' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'trade_gross_value' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'status' => \App\Enums\Legacy\InvoiceStatusEnum::Open,
            'payment_reason' => fake()->text(),
            'applicant_id' => User::factory(),
            'trade_id' => Trade::factory(),
            'target_account_snapshot' => fake()->text(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
