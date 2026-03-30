<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\User;
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
        return [
            "completion_date" => fake()->dateTime(),
            "picture" => fake()->imageUrl(),
            "description" => fake()->paragraph(30, true),
            "user_id" => User::inRandomOrder()->value('id'),
            "quest_id" => Quest::inRandomOrder()->value('id')
        ];
    }
}
