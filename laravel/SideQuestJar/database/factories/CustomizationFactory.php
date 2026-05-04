<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customization>
 */
class CustomizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customizations = [
            ["type" => "pic_border", "name" => "Golden Glow", "css" => "border: 4px solid gold; box-shadow: 0 0 10px gold;"],
            ["type" => "pic_border", "name" => "Neon Pulse", "css" => "border: 3px solid #00ff00; animation: pulse 2s infinite;"],
            ["type" => "name_style", "name" => "Shadow Text", "css" => "text-shadow: 2px 2px 4px #000000; font-weight: bold;"],
            ["type" => "name_style", "name" => "Rainbow Gradient", "css" => "background: linear-gradient(to right, red, orange, yellow, green, blue, indigo, violet); -webkit-background-clip: text; color: transparent;"],
            ["type" => "profile_bg", "name" => "Starry Night", "css" => "background-image: url('https://example.com/stars.jpg'); background-size: cover;"],
            ["type" => "profile_bg", "name" => "Modern Dark", "css" => "background-color: #121212; color: #ffffff;"]
        ];

        $customization = fake()->randomElement($customizations);

        return [
            "type" => $customization['type'],
            "picture" => "https://api.dicebear.com/7.x/bottts/svg?seed=" . urlencode($customization['name']),
            "css" => $customization['css']
        ];
    }
}
