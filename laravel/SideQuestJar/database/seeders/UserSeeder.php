<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "username" => "TBZadmin",
            "fullname" => "TBZ Admin",
            "email" => "TBZadmin@gmail.com",
            "password" => Hash::make("//bitproject2626\\"),
            "birth_date" => "2001-09-11",
            "is_admin" => true
        ]);

        User::create([
            "username" => "testuser",
            "fullname" => "Test User",
            "email" => "testuser@gmail.com",
            "password" => Hash::make("password"),
            "birth_date" => "2001-09-11",
        ]);

        

        User::factory(40)->create();
    }
}
