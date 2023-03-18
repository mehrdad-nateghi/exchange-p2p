<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'values' => fake()->text($maxNbChars = 50),
            'template_id' => EmailTemplate::factory(),
            'emailable_type' => 'App\Models\Request',
            'emailable_id' => Request::factory(),
            'created_at' => fake()->dateTime()
        ];
    }
}
