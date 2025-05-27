<?php

namespace Database\Seeders\Company;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Feature;
use Illuminate\Database\Seeder;

class DummyEcommerceActivationSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Company $company */
        $company = tenant();
        if ($company->status !== CompanyStatusEnum::ACTIVE) {
            $company->update(['status' => CompanyStatusEnum::ACTIVE]);
        }

        $feature = Feature::query()->where('name', FeatureEnum::ECOMMERCE)->first();
        if ($feature->is_active === false) {
            $feature->update(['is_active' => true]);
        }

        $user = CompanyAdmin::factory()->create();
        $token = $user->createTokenWithData('test');
        $this->command->info('Ecommerce activated for company ' . $company->id . "\nUser Token: " . $token);
    }
}
