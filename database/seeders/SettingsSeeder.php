<?php

namespace Database\Seeders;

use App\Enums\SettingsEnum;
use App\Models\Company\Settings;
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
                'value' => SettingsEnum::getValue($case->value), 'type' => SettingsEnum::getType($case->value)
            ]);
        }
    }
}
