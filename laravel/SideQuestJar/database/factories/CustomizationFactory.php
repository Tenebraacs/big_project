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
        $types = ["pic_border", "name_style", "profile_bg"];
        return [
            "type" => fake()->randomElement($types),
            "picture" => fake()->imageUrl(640,480),
            "css" => fake()->url()
        ];
    }
}
