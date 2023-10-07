<?php

namespace Tests;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use App\Models\Company\Mahalla;
use Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\LoggedExceptionCollection;
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
                PreventAccessFromCentralDomains::class
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
            '--tenants' => [$tenant->id]
        ]);
        // todo uncomment after role merge
//        Artisan::call('tenants:seed', [
//            '--class' => 'PermissionSeeder',
//            '--tenants' => [$tenant->id]
//        ]);
    }

    public function seedAddress()
    {
        Mahalla::factory()->count(1)->create();
    }

    protected function createTestResponse($response)
    {
        return tap(TestResponse::fromBaseResponse($response), function ($response) {
            $response->withExceptions(
                $this->app->bound(LoggedExceptionCollection::class)
                    ? $this->app->make(LoggedExceptionCollection::class)
                    : new LoggedExceptionCollection
            );
        });
    }

    public function createAdminUser()
    {
        // todo uncomment after role merge
//        $user = Company\User::factory()->create([
//            'role_id' => true,
//        ]);
        $user = Company\User::factory()->create();

        return $user;
    }
}
