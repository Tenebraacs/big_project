<?php

namespace Database\Seeders;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Database\Seeder;

class JarUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();

        $officialJar = Jar::where('name', "Today's Adventure")
            ->where('type', 'official_jar')
            ->first();

        if ($officialJar) {
            $officialSyncData = $users->mapWithKeys(function ($user) {
                return [
                    $user->id => [
                        'streak' => $user->username === 'ZoliProli2' ? 10 : 0,
                    ],
                ];
            })->toArray();

            $officialJar->users()->syncWithoutDetaching($officialSyncData);
        }

        Jar::where('id', '!=', optional($officialJar)->id)->get()->each(function ($jar) use ($users) {
            $selectedUsers = $users
                ->shuffle()
                ->take(rand(1, min(10, $users->count())));

            $syncData = $selectedUsers->mapWithKeys(function ($user) {
                return [
                    $user->id => [
                        'streak' => fake()->numberBetween(10, 500),
                    ],
                ];
            })->toArray();

            $jar->users()->syncWithoutDetaching($syncData);
        });
    }
}