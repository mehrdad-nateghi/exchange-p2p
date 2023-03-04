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
            'status' => \App\Enums\NotificationStatusEnum:: Unseen,
            'user_id' => '1',
            'notifiable_type' => 'App\Models\User' ,
            'notifiable_id' => 1,
            'created_at' => fake()->dateTime()
        ];
    }
}
