<?php

namespace Database\Seeders\Company;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Database\Seeder;

class DummyLoyaltyProgramActivationSeeder extends Seeder
{
    public function run(): void
    {
        /** @var Company $company */
        $company = tenant();
        if ($company->status !== CompanyStatusEnum::ACTIVE) {
            $company->update(['status' => CompanyStatusEnum::ACTIVE]);
        }

        $feature = Feature::query()->where('name', FeatureEnum::LOYALTY_PROGRAM)->first();
        if ($feature->is_active === false) {
            $feature->update(['is_active' => true]);
        }

        $user = User::factory()->create();
        $token = $user->createTokenWithData('test');
        $this->command->info('Loyalty program activated for company ' . $company->id . "\nUser Token: " . $token);
    }
}
