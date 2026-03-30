<?php

namespace Database\Factories;

use App\Models\Jar;
use App\Models\Quest;
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
            "quest_id" => Quest::inRandomOrder()->value('id'),
            "jar_id" => Jar::inRandomOrder()->value('id')
        ];
    }
}
