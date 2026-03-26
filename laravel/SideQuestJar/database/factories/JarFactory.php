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
        $types = ["public_jar", "private_jar", "friends_only_jar", "invite_only_jar", "official_jar"];
        return [
            "name" => fake()->name(),
            "description" => fake()->paragraph(30, true),
            "picture" => fake()->imageUrl(640, 480),
            "type" => fake()->randomElement($types)
        ];
    }
}
