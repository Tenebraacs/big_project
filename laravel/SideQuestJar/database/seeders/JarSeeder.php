<?php

namespace Database\Seeders;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $zoli = User::where('username', 'ZoliProli')->first();

        Jar::create([
            "name" => "JAR",
            "description" => "This is the Main Jar used on the front page and is supposed to be the soul and base of this wole thing (i need ideas for this)",
            "user_id" => $zoli->id
        ]);

        Jar::factory(100)->create();
    }
}
