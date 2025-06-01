<?php

namespace Database\Seeders\Main;

use App\Enums\Main\SettingsEnum;
use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SettingsEnum::cases() as $case) {
            Settings::query()->firstOrCreate([
                'name' => $case->value,
            ], [
                'value' => SettingsEnum::getDefaultValue($case),
                'type' => SettingsEnum::getType($case),
            ]);
        }

        Settings::query()->whereNotIn('name', SettingsEnum::cases())->delete();

        Log::info('SettingsSeeder completed successfully.');
    }
}
