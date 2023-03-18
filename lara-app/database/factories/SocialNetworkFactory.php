<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialNetwork>
 */
class SocialNetworkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->word ,
            'url' => fake()->text($maxNbChars = 50) ,
            'status' => true ,
            'logo_url' => fake()->text($maxNbChars = 50)
        ];
    }
}
