<?php

namespace Database\Seeders\Company;

use App\Models\BaseFeature;
use App\Models\Company\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        /** @var BaseFeature $baseFeature */
        foreach (BaseFeature::query()->get()->all() as $baseFeature) {
            /** @var Feature|null $feature */
            $feature = Feature::query()->find($baseFeature->id);

            if (! $feature) {
                $feature = Feature::query()->create([
                    'id' => $baseFeature->id,
                    'name' => $baseFeature->name,
                    'description' => $baseFeature->description,
                    'is_active' => app()->runningUnitTests() || $baseFeature->is_default_active,
                    'original_amount' => $baseFeature->original_amount,
                    'original_data' => $baseFeature->original_data,
                ]);
            } else {
                $feature->update([
                    'name' => $baseFeature->name,
                    'description' => $baseFeature->description,
                    'original_amount' => $baseFeature->original_amount,
                    'original_data' => $baseFeature->original_data,
                ]);
            }

            $feature->modules()->sync(
                $baseFeature->modules()->pluck('base_modules.id')
            );
        }

        Feature::query()->whereIn('id', BaseFeature::query()->pluck('id'))->forceDelete();
    }
}
