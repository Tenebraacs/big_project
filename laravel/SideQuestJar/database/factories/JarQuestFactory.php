<?php

namespace Database\Factories;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JarQuest>
 */
class JarQuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quests = [
            ["title" => "Morning Stretch", "description" => "Start your day with a 5-minute full-body stretch."],
            ["title" => "Hydration Hero", "description" => "Drink a full glass of water right now."],
            ["title" => "Digital Detox", "description" => "Stay away from social media for the next hour."],
            ["title" => "Kindness Note", "description" => "Send a quick thank-you message to someone you appreciate."],
            ["title" => "Mindful Breathing", "description" => "Take 10 deep, controlled breaths."],
            ["title" => "Healthy Snack", "description" => "Eat a piece of fruit or some nuts instead of processed snacks."],
            ["title" => "Tidy Up", "description" => "Spend 2 minutes decluttering your immediate workspace."],
            ["title" => "Learn Something New", "description" => "Look up a random fact or a new word."],
            ["title" => "Walk It Out", "description" => "Go for a quick 10-minute walk around the block."],
            ["title" => "Gratitude Journal", "description" => "Write down three things you are grateful for today."]
        ];

        $quest = fake()->randomElement($quests);
        
        return [
            "jar_id" => Jar::whereNotIn('name', ["Today's Avdenture"])->inRandomOrder()->value('id') ?? Jar::factory(),
            "title" => $quest['title'],
            "description" => $quest['description'],
            "created_by" => User::inRandomOrder()->value('id') ?? User::factory()
        ];
    }
}
