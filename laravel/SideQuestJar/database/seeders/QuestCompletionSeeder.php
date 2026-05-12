<?php

namespace Database\Seeders;

use App\Models\Jar;
use App\Models\JarQuest;
use App\Models\QuestCompletion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class QuestCompletionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('username', 'testuser')->first();

        $officialJar = Jar::where('name', "Today's Adventure")
            ->where('type', 'official_jar')
            ->first();

        if (!$user || !$officialJar) {
            return;
        }

        $quests = JarQuest::where('jar_id', $officialJar->id)
            ->orderBy('id')
            ->get();

        if ($quests->isEmpty()) {
            return;
        }

        QuestCompletion::where('user_id', $user->id)
            ->whereHas('jarQuest', function ($query) use ($officialJar) {
                $query->where('jar_id', $officialJar->id);
            })
            ->delete();

        for ($i = 10; $i >= 1; $i--) {
            $date = Carbon::today()->subDays($i)->setTime(12, 0, 0);

            $quest = $quests[($i - 1) % $quests->count()];

            QuestCompletion::create([
                'completion_date' => $date,
                'picture' => 'https://via.placeholder.com/640x480.png/00ee00?text=Quest+Completion',
                'description' => 'Seeded streak test completion for testuser on ' . $date->toDateString(),
                'type' => 'public_post',
                'user_id' => $user->id,
                'jar_quest_id' => $quest->id,
            ]);
        }
    }
}