<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $zoli = User::where('username', 'ZoliProli')->first();

        UserRoles::create([
            "user_id" => $zoli->id,
            "role" => "admin",
            "jar_id" => 1
        ]);

        UserRoles::factory(100)->create();
    }
}
