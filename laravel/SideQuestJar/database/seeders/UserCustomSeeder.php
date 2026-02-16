<?php

namespace Database\Seeders;

use App\Models\UserCustom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCustomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserCustom::factory(100)->create();
    }
}
