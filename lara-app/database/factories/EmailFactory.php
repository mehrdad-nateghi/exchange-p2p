<?php

namespace Database\Factories;

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
            'values' => fake()->text($maxNbChars = 50),
            'template_id' => 1 ,
            'emailable_type' => 'App\Models\User' ,
            'emailable_id' => 1,
            'created_at' => fake()->dateTime()
        ];
    }
}
