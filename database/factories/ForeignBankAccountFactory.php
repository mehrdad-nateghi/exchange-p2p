<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForeignBankAccount>
 */
class ForeignBankAccountFactory extends Factory
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
            'iban' => $this->faker->iban,
            'bic' => $this->faker->swiftBicNumber,
            'is_active' => true,
        ];
    }
}
