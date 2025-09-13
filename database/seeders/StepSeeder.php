<?php

namespace Database\Seeders;

use App\Enums\StepOwnerEnum;
use App\Models\Step;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $durationMinutes = 5;

        $steps = [
            [
                'name' => 'Pay Toman to System',
                'description' => 'The buyer must deposit the Toman amount into the system account.',
                'priority' => 1,
                'owner' => StepOwnerEnum::BUYER->value,
                'duration_minutes' => $durationMinutes,
                'is_active' => true,
            ],
            [
                'name' => 'Transfer Currency and Upload Receipt',
                'description' => "The seller must transfer the currency to the buyer\'s account and upload the receipt.",
                'priority' => 2,
                'owner' => StepOwnerEnum::SELLER->value,
                'duration_minutes' => $durationMinutes,
                'is_active' => true,
            ],
            [
                'name' => 'Confirm or Reject Currency Receipt',
                'description' => 'The buyer must confirm or reject the receipt of the currency.',
                'priority' => 3,
                'owner' => StepOwnerEnum::BUYER->value,
                'duration_minutes' => $durationMinutes,
                'is_active' => true,
            ],
            [
                'name' => 'Pay Toman to Seller',
                'description' => 'The system makes the final payment to the seller.',
                'priority' => 4,
                'owner' => StepOwnerEnum::SYSTEM->value,
                'duration_minutes' => $durationMinutes,
                'is_active' => true,
            ],
        ];

        foreach ($steps as $step) {
            Step::create($step);
        }
    }
}
