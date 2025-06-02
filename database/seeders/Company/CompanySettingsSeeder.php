<?php

namespace Database\Seeders\Company;

use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class CompanySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // todo: remove this after first run
        CompanySettings::query()->where('name', 'pinfl_cards_update_months')->delete();
        // end todo

        try {
            foreach (CompanySettingsEnum::cases() as $case) {
                CompanySettings::query()->firstOrCreate([
                    'name' => $case->value,
                ], [
                    'value' => CompanySettingsEnum::getDefaultValue($case),
                    'type' => CompanySettingsEnum::getType($case),
                ]);
            }

            Log::info('Company settings seeded successfully.');

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
