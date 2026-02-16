<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jar>
 */
class JarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ["public", "private", "friends_only", "invite_only", "offical"];
        return [
            "name" => fake()->name(),
            "description" => fake()->paragraph(30, true),
            "picture" => fake()->imageUrl(640, 480),
            "user_id" => fake()->numberBetween(1,100),
            "type" => fake()->randomElement($types)
        ];
    }
}
