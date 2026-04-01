<?php

namespace Database\Factories;

use App\Models\Jar;
use App\Models\JarQuest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Like>
 */
class LikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ["jar", "quest"];
        return [
            "user_id" => User::inRandomOrder()->value('id'),
            "type" => fake()->randomElement($types),
            "jar_quest_id" => JarQuest::inRandomOrder()->value('id'),
            "jar_id" => Jar::inRandomOrder()->value('id')
        ];
    }
}
