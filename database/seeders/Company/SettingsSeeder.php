<?php

namespace Database\Seeders\Company;

use App\Enums\SettingsType;
use App\Models\Company\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settings::query()->firstOrCreate([
            'name' => 'partner_api_maintenance_mode'
        ], ['value' => 0, 'type' => SettingsType::BOOLEAN]);
        Settings::query()->firstOrCreate([
            'name' => 'partner_app_min_version'
        ], ['value' => 1, 'type' => SettingsType::INT]);
    }
}
