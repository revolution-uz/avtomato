<?php

namespace Database\Seeders\Company;

use App\Enums\Company\FeatureEnum;
use App\Models\Company\Features;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        foreach (FeatureEnum::cases() as $case) {
            Features::query()->firstOrCreate([
                'name' => $case->value
            ], [
                'is_active' => false,
            ]);
        }
    }
}
