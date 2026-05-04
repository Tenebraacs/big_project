<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $username = fake()->unique()->userName();
        return [
            'username' => $username,
            'nickname' => fake()->firstName(),
            'fullname' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'profile_picture' => "https://api.dicebear.com/7.x/avataaars/svg?seed=$username",
            'birth_date' => fake()->date("Y-m-d"),
            'joined_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'user_xp' => fake()->numberBetween(0, 5000),
            'is_customized' => fake()->boolean(20),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
