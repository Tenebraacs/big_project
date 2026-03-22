<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            "password" => "123//ZoliProli//123",
            "birth_date" => "2001-09-11",
        ]);
        User::factory(100)->create();
    }
}
