<?php

namespace Database\Seeders;

use App\Models\QuestCompletion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestCompletionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        QuestCompletion::factory(100)->create();
    }
}
