<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $achievements = [
            ["name" => "First Step", "description" => "Complete your very first quest."],
            ["name" => "Streak Starter", "description" => "Complete quests 3 days in a row."],
            ["name" => "Jar Opener", "description" => "Create your first custom jar."],
            ["name" => "Socialite", "description" => "Add 5 friends to your network."],
            ["name" => "Explorer", "description" => "Complete quests from 5 different jars."],
            ["name" => "Master of Jars", "description" => "Complete 50 quests in total."],
            ["name" => "Early Bird", "description" => "Complete a quest before 8 AM."],
            ["name" => "Night Owl", "description" => "Complete a quest after 10 PM."],
            ["name" => "Philanthropist", "description" => "Complete 10 quests in a 'Kindness' jar."],
            ["name" => "Consistency King", "description" => "Maintain a 30-day streak."],
            ["name" => "The Chef", "description" => "Complete 5 quests in a 'Kitchen' jar."],
            ["name" => "Gym Rat", "description" => "Complete 10 fitness-related quests."],
            ["name" => "Zen Master", "description" => "Complete 10 meditation or mindfulness quests."],
            ["name" => "Bookworm", "description" => "Finish reading a book from your reading jar."],
            ["name" => "Globetrotter", "description" => "Complete a quest in 3 different countries."],
            ["name" => "Handyman", "description" => "Fix 3 things around the house."],
            ["name" => "Green Thumb", "description" => "Water your plants for a whole week."],
            ["name" => "Polyglot", "description" => "Learn 50 new words in a foreign language."],
            ["name" => "Investor", "description" => "Save your first $100 through financial quests."],
            ["name" => "Helping Hand", "description" => "Assist a friend with one of their quests."]
        ];

        $achievement = fake()->unique()->randomElement($achievements);

        return [
            "name" => $achievement['name'],
            "description" => $achievement['description'],
            "picture" => "https://api.dicebear.com/7.x/icons/svg?seed=" . urlencode($achievement['name']),
            "user_completion" => fake()->numberBetween(1, 100)
        ];
    }
}
