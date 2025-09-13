<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DepositReason>
 */
class DepositReasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $predefinedTitles = [
            'Rent',
            'University fee',
            'Payment for friend',
            'Payment for family',
            'Repayment of the loan',
            'Birthday gift',
            'Donation',
            'Purchase of goods'
        ];

        static $index = 0;

        $title = $predefinedTitles[$index];
        $index = ($index + 1) % count($predefinedTitles);

        return [
            'title' => $title,
        ];
    }
}
