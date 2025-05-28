<?php

namespace Database\Seeders\Main;

use App\Enums\FeatureEnum;
use App\Models\BaseFeature;
use App\Models\Company;
use App\Models\Company\Feature;
use Illuminate\Database\Seeder;

class RenameUniredPinflPayFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $feature = FeatureEnum::UNIRED_PINFL_PAY;

        BaseFeature::query()->where('name', 'unired_pay')->update(['name' => $feature->value]);

        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::query()->where('name', 'unired_pay')->update(['name' => $feature->value]);
        }
    }
}
