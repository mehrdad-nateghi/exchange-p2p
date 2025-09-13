<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TradeConstraint>
 */
class TradeConstraintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'payment_rial_time_constraint' => fake()->randomNumber(2),
            'payment_currency_time_constraint' => fake()->randomNumber(2),
            'confirmation_receipt_time_constraint'=> fake()->randomNumber(2),
            'system_payment_time_constraint' => fake()->randomNumber(2),
            'updated_at' => fake()->dateTime()
        ];
    }
}
