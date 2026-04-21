<?php

namespace Database\Seeders;

use App\Models\Jar;
use App\Models\JarUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JarUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    
    public function run(): void
    {
        $users = User::where('is_admin', false)->pluck('id');

        Jar::all()->each(function ($jar) use ($users){

            // Pick random users for jar

            $selectedUsers = $users
                ->shuffle()
                ->take(rand(1, min(10, $users->count())));
            // Prepare Pivot data

            $syncData = $selectedUsers->mapWithKeys(function ($userId) {
                return [
                    $userId => [
                        'streak' => fake()->numberBetween(10, 500),
                    ]
                ];
            })->toArray();

            // Attach users without duplicates
            $jar->users()->syncWithoutDetaching($syncData);

        });

        
    }
}
