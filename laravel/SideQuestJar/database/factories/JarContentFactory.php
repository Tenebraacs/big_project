<?php

namespace Database\Factories;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JarContent>
 */
class JarContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $pictures = ["https://i.ibb.co/JjJczBgX/Hug-ATree.png", "https://i.ibb.co/wN9GKctB/Rope-AJump.png"];
        
        return [
            "jar_id" => Jar::whereNotIn('name', ["Today's Avdenture"])->inRandomOrder()->value('id'),
            "title" => fake()->unique()->jobTitle(),
            "description" => fake()->text(),
            "picture" => fake()->randomElement($pictures),
            "likes" => fake()->numberBetween(20,1000),
            "created_by" => User::inRandomOrder()->value('id')
        ];
    }
}
