<?php

namespace Tests;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use App\Models\Company\Mahalla;
use App\Models\Company\Rbac\Role;
use Database\Seeders\Company\AdminRoleSeeder;
use Database\Seeders\Company\FeatureSeeder;
use Database\Seeders\Company\PermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $tenancy = false;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
            $this->withoutMiddleware([
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
            ]);
        }
    }

    public function initializeTenancy()
    {
        $tenant = Company::first() ?? Company::factory()->create([
            'name' => 'Test Company',
            'owner_phone' => '0123456789',
            'owner_name' => 'Test Owner',
            'is_active' => CompanyStatusEnum::ACTIVE,
        ]);

        tenancy()->initialize($tenant);

        Artisan::call('tenants:migrate-fresh', [
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

    }

    public function seedAddress()
    {
        Mahalla::factory()->count(1)->create();
    }

    public function createAdminUser()
    {
        return Company\User::factory()->create([
            'role_id' => Role::getAdmin()->id,
        ]);
    }
}
