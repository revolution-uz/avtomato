<?php

namespace Database\Seeders\Main;

use App\Enums\FeatureEnum;
use App\Enums\ModuleEnum;
use App\Models\BaseFeature;
use App\Models\BaseModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BaseFeatureSeeder extends Seeder
{
    public function run(): void
    {
        foreach (FeatureEnum::cases() as $case) {
            /** @var BaseFeature $feature */
            $feature = BaseFeature::query()->firstOrCreate([
                'name' => $case->value,
            ], [
                'is_default_active' => ModuleEnum::getModuleForFeature($case) === ModuleEnum::cases(),
                'original_amount' => 0,
            ]);
            $feature->modules()->sync(
                BaseModule::query()->whereIn('name', ModuleEnum::getModuleForFeature($case))->pluck('id')
            );
        }

        BaseFeature::query()->whereNotIn('name', FeatureEnum::cases())->forceDelete();

        Log::info('BaseFeatureSeeder completed successfully.');
    }
}
