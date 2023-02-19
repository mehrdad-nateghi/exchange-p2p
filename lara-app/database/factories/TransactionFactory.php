<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            'amount' => fake()->randomFloat($nbMaxDecimals = 2 , $min = 0, $max = 99,999,999,999.99),
            'description' => fake()->text($maxNbChars = 50),
            'status' => \App\Enums\TransactionStatusEnum::Successful,
            'invoice_id' => 1,
            'transaction_method_id' => 1,
            'created_at' => fake()->dateTime()
        ];
    }
}
