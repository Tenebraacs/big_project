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
        $jarNames = [
            "Daily Hustle", "Mindful Moments", "Fitness Fanatic", "Kitchen Wizard",
            "Creative Sparks", "Social Butterfly", "Bookworm's Journey", "Nature Explorer",
            "Home Sanctuary", "Tech Explorer", "Financial Freedom", "Kindness Jar",
            "Language Learner", "Travel Dreams", "Self-Care Sunday", "Night Owl Rituals"
        ];
        
        $types = ["public_jar", "private_jar", "friends_only_jar", "invite_only_jar"];
        $descriptions = [
            "A collection of tasks to keep you productive and motivated every day.",
            "Small actions to help you stay present and grounded.",
            "Quests designed to keep your body moving and healthy.",
            "Delicious challenges to spice up your culinary skills.",
            "Daily prompts to ignite your creativity and passion projects.",
            "Fun ways to connect with friends and meet new people.",
            "Track your progress and discover new worlds through reading.",
            "Get outside and appreciate the beauty of the natural world."
        ];
        
        return [
            'name'        => fake()->randomElement($jarNames),
            'description' => fake()->randomElement($descriptions),
            'type'        => fake()->randomElement($types),
            'created_by'  => User::factory(),
        ];
    }
}
