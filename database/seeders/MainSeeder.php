<?php

namespace Database\Seeders;

use Database\Seeders\Main\SettingsSeeder;
use Illuminate\Database\Seeder;

class MainSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SettingsSeeder::class);
    }
}
