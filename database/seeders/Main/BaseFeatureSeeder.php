<?php

namespace Database\Seeders\Main;

use App\Enums\FeatureEnum;
use App\Enums\ModuleEnum;
use App\Models\BaseFeature;
use App\Models\BaseModule;
use Illuminate\Database\Seeder;

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
                'description' => $case->getDefaultDescription(),
                'original_amount' => 0,
            ]);
            $feature->modules()->sync(
                BaseModule::query()->whereIn('name', ModuleEnum::getModuleForFeature($case))->pluck('id')
            );
        }
    }
}
