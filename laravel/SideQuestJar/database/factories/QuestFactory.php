<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quest>
 */
class QuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $xpgain = [15, 25, 50, 100, 250, 500, 1000];
        return [
            "name" => fake()->name(),
            "description" => fake()->paragraph(30, true),
            "completions" => fake()->numberBetween(1,100),
            "jar_id" => fake()->numberBetween(1,100),
            "xp_gain" => fake()->randomElement($xpgain)
        ];
    }
}
