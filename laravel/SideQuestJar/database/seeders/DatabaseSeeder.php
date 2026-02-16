<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Jar;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();

        $this->call([
            JarSeeder::class,
            AchievementSeeder::class,
            AchievementCompletionSeeder::class,
            JarUserSeeder::class,
            LibrarySeeder::class,
            QuestSeeder::class,
            QuestCompletionSeeder::class,
            LikeSeeder::class,
            CustomizationSeeder::class,
            UserCustomSeeder::class,
            UserRolesSeeder::class
        ]);
    }
}
