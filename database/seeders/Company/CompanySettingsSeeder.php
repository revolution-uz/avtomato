<?php

namespace Database\Seeders\Company;

use App\Enums\Main\SettingsEnum;
use App\Models\Company\CompanySettings;
use Illuminate\Database\Seeder;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SettingsEnum::cases() as $case) {
            CompanySettings::query()->firstOrCreate([
                'name' => $case->value,
            ], [
                'value' => SettingsEnum::getDefaultValue($case),
                'type' => SettingsEnum::getType($case),
            ]);
        }
    }
}
