<?php

namespace Database\Seeders;



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
        

        $this->call([
            UserSeeder::class,
            JarSeeder::class,
            JarQuestSeeder::class,
            JarUserSeeder::class,          
            AchievementSeeder::class,    
            AchievementCompletionSeeder::class,                
            QuestCompletionSeeder::class,
            //LikeSeeder::class,
            CustomizationSeeder::class,
            UserCustomSeeder::class,
            
        ]);
    }
}
