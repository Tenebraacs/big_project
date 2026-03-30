<?php

namespace Database\Seeders;

use App\Models\Jar;
use App\Models\JarQuest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JarQuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$zoli = User::where('username', 'ZoliProli')->first();
        //https://i.ibb.co/Ngk3xx10/Godly-Pfp.png Zoli Kep
        $mainjar = Jar::where('name', "Today's Adventure")->where('type', 'official_jar')->first();

        JarQuest::create([
            "jar_id" => $mainjar->id,
            "title" => "Hug a tree",
            "picture" => "https://i.ibb.co/JjJczBgX/Hug-ATree.png",
            "description" => "Go outside and hug a tree",
            "likes" => 222
        ]);

        JarQuest::create([
            "jar_id" => $mainjar->id,
            "title" => "RopeAJump",
            "picture" => "https://i.ibb.co/wN9GKctB/Rope-AJump.png",
            "description" => "Go outside and rope a jump",
            "likes" => 22
        ]);

        JarQuest::factory(100)->create();
    }
}
