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
            "name" => "Today's Adventure",
            "type" => "official_jar",
            "created_by" => $zoli->id
        ]);

        Jar::factory(20)->create();
    }
}
