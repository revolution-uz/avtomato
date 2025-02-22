<?php

namespace Tests;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use App\Models\Company\Rbac\Role;
use Database\Seeders\Company\AdminRoleSeeder;
use Database\Seeders\Company\BranchSeeder;
use Database\Seeders\Company\CompanySettingsSeeder;
use Database\Seeders\Company\FeatureSeeder;
use Database\Seeders\Company\ModuleSeeder;
use Database\Seeders\Company\PermissionSeeder;
use Database\Seeders\Company\SmsTemplateSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected bool $tenancy = false;
    protected bool $multiTenancies = false;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');
        Config::set('cache.default', 'array');

        if ($this->tenancy) {
            $this->initializeTenancy();
            if ($this->multiTenancies) {
                $this->initializeTenancy(2);
            }
            $this->withoutMiddleware([
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ]);
        }
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function initializeTenancy(int $companyId = 1): void
    {
        /** @var Company $tenant */
        $tenant = Company::firstOrCreate(['id' => $companyId], Company::factory()->make()->toArray());

        if ($tenant->status !== CompanyStatusEnum::ACTIVE) {
            $tenant->update(['status' => CompanyStatusEnum::ACTIVE]);
        }

        tenancy()->initialize($tenant);

        Artisan::call('tenants:migrate-fresh', ['--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => ModuleSeeder::class, '--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => FeatureSeeder::class, '--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => PermissionSeeder::class, '--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => AdminRoleSeeder::class, '--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => SmsTemplateSeeder::class, '--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => CompanySettingsSeeder::class, '--tenants' => [$tenant->id]]);
        Artisan::call('tenants:seed', ['--class' => BranchSeeder::class, '--tenants' => [$tenant->id]]);
    }

    public function createAdminUser()
    {
        $admin = Company\CompanyAdmin::query()->where('role_id', Role::getAdmin()->id)->first();

        return $admin ?? Company\CompanyAdmin::factory()->create([
            'role_id' => Role::getAdmin()->id,
            'password' => Hash::make('password'),
        ]);
    }

    public function createSimpleAdminUser($roleId = null)
    {
        return Company\CompanyAdmin::factory()->create([
            'role_id' => $roleId,
            'password' => Hash::make('password'),
        ]);
    }

    public function createEcommerceClientUser(bool $hasCustomer = false): Company\Ecommerce\User
    {
        return Company\Ecommerce\User::query()
            ->when($hasCustomer, fn ($q) => $q->whereHas('customer'))
            ->firstOr(fn () => $hasCustomer
                ? Company\Ecommerce\User::factory()->withCustomer()->create()
                : Company\Ecommerce\User::factory()->create());
    }
}
