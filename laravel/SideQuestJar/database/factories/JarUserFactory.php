<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JarUser>
 */
class JarUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "jar_id" => fake()->numberBetween(1,100),
            "user_id" => fake()->numberBetween(1,100),
            "streak" => fake()->numberBetween(1,365)
        ];
    }
}
