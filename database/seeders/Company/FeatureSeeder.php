<?php

namespace Database\Seeders\Company;

use App\Enums\Company\FeaturesEnum;
use App\Models\Company\Features;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        foreach (FeaturesEnum::cases() as $case) {
            Features::query()->firstOrCreate([
                'name' => $case->value
            ], [
                'is_active' => false,
            ]);
        }
    }
}
