<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->word,
            'body' => fake()->text($maxNbChars = 50) ,
            'class' => \App\Enums\NotificationClassEnum:: Information,
            'status' => \App\Enums\NotificationStatusEnum:: Unseen,
            'user_id' => '1',
            'created_at' => fake()->dateTime()
        ];
    }
}
