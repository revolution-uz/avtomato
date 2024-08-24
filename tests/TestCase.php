<?php

namespace Tests;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use App\Models\Company\Rbac\Role;
use Database\Seeders\Company\AdminRoleSeeder;
use Database\Seeders\Company\FeatureSeeder;
use Database\Seeders\Company\ModuleSeeder;
use Database\Seeders\Company\PermissionSeeder;
use Database\Seeders\Company\SmsTemplateSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected bool $tenancy = false;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed');

        if ($this->tenancy) {
            $this->initializeTenancy();
            $this->withoutMiddleware([
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ]);
        }
    }

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function initializeTenancy(): void
    {
        $tenant = Company::first() ?? Company::factory()->create([
            'name' => 'Test Company',
            'owner_phone' => '0123456789',
            'owner_name' => 'Test Owner',
            'status' => CompanyStatusEnum::ACTIVE,
        ]);

        tenancy()->initialize($tenant);

        Artisan::call('tenants:migrate-fresh', [
            '--tenants' => [$tenant->id],
        ]);
        Artisan::call('tenants:seed', [
            '--class' => ModuleSeeder::class,
            '--tenants' => [$tenant->id],
        ]);
        Artisan::call('tenants:seed', [
            '--class' => FeatureSeeder::class,
            '--tenants' => [$tenant->id],
        ]);
        Artisan::call('tenants:seed', [
            '--class' => PermissionSeeder::class,
            '--tenants' => [$tenant->id],
        ]);
        Artisan::call('tenants:seed', [
            '--class' => AdminRoleSeeder::class,
            '--tenants' => [$tenant->id],
        ]);
        Artisan::call('tenants:seed', [
            '--class' => SmsTemplateSeeder::class,
            '--tenants' => [$tenant->id],
        ]);

    }

    public function createAdminUser()
    {
        $admin = Company\CompanyAdmin::query()->where('role_id', Role::getAdmin()->id)->first();

        return $admin ?? Company\CompanyAdmin::factory()->create([
            'role_id' => Role::getAdmin()->id,
            'password' => Hash::make('password'),
        ]);
    }

    public function createSimpleAdminUser()
    {
        return Company\CompanyAdmin::factory()->create([
            'role_id' => null,
            'password' => Hash::make('password'),
        ]);
    }
}
