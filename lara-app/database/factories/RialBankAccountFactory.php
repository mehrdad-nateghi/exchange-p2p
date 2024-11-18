<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RialBankAccount>
 */
class RialBankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'holder_name' => $this->faker->name,
            'bank_name' => $this->faker->company,
            'card_number' => $this->faker->creditCardNumber(),
            'iban' => $this->faker->iban('IR'),
            'account_no' => $this->faker->bankAccountNumber,
            'is_active' => true,
        ];
    }
}
