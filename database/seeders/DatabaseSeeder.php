<?php

namespace Database\Seeders;

use Database\Seeders\Main\BaseFeatureSeeder;
use Database\Seeders\Main\BaseModuleSeeder;
use Database\Seeders\Main\BaseSmsTemplateSeeder;
use Database\Seeders\Main\RenameUniredPinflPayFeatureSeeder;
use Database\Seeders\Main\SettingsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // todo: remove this after first run
        $this->call(RenameUniredPinflPayFeatureSeeder::class);

        $this->call(SettingsSeeder::class);
        $this->call(BaseModuleSeeder::class);
        $this->call(BaseFeatureSeeder::class);
        $this->call(BaseSmsTemplateSeeder::class);
    }
}
