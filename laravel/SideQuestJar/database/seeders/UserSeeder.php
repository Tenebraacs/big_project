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
            "username" => "ZoliProli",
            "fullname" => "Zoli Proli",
            "email" => "ZoliProli@gmail.com",
            "password" => Hash::make("123//ZoliProli//123"),
            "birth_date" => "2001-09-11",
            "is_admin" => true
        ]);

        User::create([
            "username" => "ZoliProli2",
            "fullname" => "Zoli Proli",
            "email" => "ZoliProli2@gmail.com",
            "password" => Hash::make("123//ZoliProli//123"),
            "birth_date" => "2001-09-11",
        ]);

        

        User::factory(40)->create();
    }
}
