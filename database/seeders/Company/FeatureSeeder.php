<?php

namespace Database\Seeders\Company;

use App\Models\BaseFeature;
use App\Models\Company\Feature;
use App\Models\Company\Rbac\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        try {
            /** @var BaseFeature $baseFeature */
            foreach (BaseFeature::query()->get()->all() as $baseFeature) {
                /** @var Feature|null $feature */
                $feature = Feature::query()->find($baseFeature->id);

                if (! $feature) {
                    $feature = Feature::query()->create([
                        'id' => $baseFeature->id,
                        'name' => $baseFeature->name,
                        'is_active' => app()->runningUnitTests() || $baseFeature->is_default_active,
                        'original_amount' => $baseFeature->original_amount,
                        'original_data' => $baseFeature->original_data,
                    ]);
                } else {
                    $data = [
                        'name' => $baseFeature->name,
                        'original_amount' => $baseFeature->original_amount,
                    ];
                    if ($baseFeature->original_data) {
                        $data['original_data'] = $baseFeature->original_data;
                    }
                    $feature->update($data);
                }

                $feature->modules()->sync(
                    $baseFeature->modules()->pluck('base_modules.id')
                );
            }

            Permission::query()->whereNotIn('feature_id', BaseFeature::query()->pluck('id'))->forceDelete();
            Feature::query()->whereNotIn('id', BaseFeature::query()->pluck('id'))->forceDelete();

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
