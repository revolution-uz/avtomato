<?php

namespace Database\Seeders\Company;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use Illuminate\Database\Seeder;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (CompanySettingsEnum::cases() as $case) {
            CompanySettings::query()->updateOrCreate([
                'name' => $case->value,
            ], [
                'value' => CompanySettingsEnum::getDefaultValue($case),
                'type' => CompanySettingsEnum::getType($case),
            ]);
        }
    }
}
