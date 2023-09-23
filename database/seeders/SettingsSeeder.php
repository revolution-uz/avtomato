<?php

namespace Database\Seeders;

use App\Enums\Main\SettingsEnum;
use App\Models\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SettingsEnum::cases() as $case) {
            Settings::query()->firstOrCreate([
                'name' => $case->value
            ], [
                'value' => SettingsEnum::getDefaultValue($case),
                'type' => SettingsEnum::getType($case)
            ]);
        }
    }
}
