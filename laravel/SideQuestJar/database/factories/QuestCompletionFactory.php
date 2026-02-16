<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestCompletion>
 */
class QuestCompletionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $post_types = ["friends_only", "public", "private"];
        return [
            "completion_date" => fake()->dateTime(),
            "post_type" => fake()->randomElement($post_types),
            "picture" => fake()->imageUrl(),
            "description" => fake()->paragraph(30, true),
            "user_id" => fake()->numberBetween(1,100),
            "quest_id" => fake()->numberBetween(1,100)
        ];
    }
}
