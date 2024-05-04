<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
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
            'status' => \App\Enums\old\NotificationStatusEnum:: Unseen,
            'user_id' => User::factory(),
            'notifiable_type' => 'App\Models\Request',
            'notifiable_id' => Request::factory(),
            'created_at' => fake()->dateTime()
        ];
    }
}
