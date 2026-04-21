<?php

namespace Database\Factories;

use App\Models\Jar;
use App\Models\User;
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
            //NINCS HASZNALVA

            "jar_id" => Jar::inRandomOrder()->value('id'),
            "user_id" => User::inRandomOrder()->value('id'),
            "streak" => fake()->numberBetween(10,500)
        ];
    }
}
