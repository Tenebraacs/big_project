<?php

namespace Database\Seeders;

use App\Models\JarUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JarUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JarUser::factory(20)->create();
        //Todo ugyanaz a user ugyanabban a tablaban = NO NO
    }
}
