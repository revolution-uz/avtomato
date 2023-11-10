<?php

namespace Database\Seeders\Company;

use App\Enums\Company\FeatureEnum;
use App\Models\Company\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        foreach (FeatureEnum::cases() as $case) {
            Feature::query()->firstOrCreate([
                'name' => $case->value,
            ], [
                'is_active' => app()->runningUnitTests() || app()->environment('local'),
            ]);
        }
    }
}
