<?php

namespace Tests\Unit;

use App\Models\Company\User;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RbacTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * Tests all routes to check if they have rbac
     *
     * @test
     */
    public function forbidden_access(): void
    {
        $routes = Route::getRoutes();
        $user = User::factory()->create();

        $skip_routes = [
            'api/v1/login',
            'api/v1/logout',
            'api/v1/docs',
            'api/v1/profile/me',
            'api/v1/info/settings',
            'api/v1/info/regions',
            'api/v1/info/districts',
            'api/v1/info/mahallas',
        ];

        /** @var RouteCollection $routes */
        foreach ($routes->getRoutes() as $route) {
            if (str_starts_with($route->uri, 'api/v1') && ! in_array($route->uri, $skip_routes, true)) {

                $parameters = [];
                foreach ($route->parameterNames() as $parameterName) {
                    $parameters[$parameterName] = 1;
                }
                $method = strtolower($route->methods()[0]);
                $response = $this->actingAs($user)->$method(route($route->getName(), $parameters));

                //                echo $route->getName() . PHP_EOL;
                $response->assertForbidden();
            }
        }
    }
}
