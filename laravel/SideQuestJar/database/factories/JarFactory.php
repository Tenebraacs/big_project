<?php

namespace Database\Factories;

use App\Models\User;
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
        $types = ["public_jar", "private_jar", "friends_only_jar", "invite_only_jar"];
        
        return [
            "name" => fake()->name(),     
            "type" => fake()->randomElement($types),
            "likes" => fake()->numberBetween(20,1000),
            "created_by" => User::inRandomOrder()->value('id')
        ];
    }
}
