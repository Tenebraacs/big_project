<?php

namespace Database\Seeders;

use App\Models\AchievementCompletion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementCompletionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AchievementCompletion::factory(100)->create();
    }
}
