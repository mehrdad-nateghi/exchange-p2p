<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\TransactionMethod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'support_id' => config('constants.SupportId_Prefixes.Transaction_Pr'). Str::uuid(),
            'amount' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'description' => fake()->text($maxNbChars = 50),
            'status' => \App\Enums\old\TransactionStatusEnum::Successful,
            'invoice_id' => Invoice::factory(),
            'transaction_method_id' => TransactionMethod::factory(),
            'created_at' => fake()->dateTime()
        ];
    }
}
