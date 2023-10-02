<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapApiRoutes(): void
    {
        foreach ($this->centralDomains() as $domain) {
            Route::middleware('api')
                ->domain($domain)
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        }

        Route::middleware('api')
            ->namespace($this->namespace)
            ->middleware([InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class])
            ->prefix('api/v1')
            ->group(base_path('routes/partner-api.php'));
    }

    protected function mapWebRoutes(): void
    {
        foreach ($this->centralDomains() as $domain) {
            Route::middleware('web')
                ->domain($domain)
                ->namespace($this->namespace)
                ->group(base_path('routes/website.php'));
        }

        Route::middleware('web')
            ->namespace($this->namespace)
            ->middleware([InitializeTenancyByDomain::class, PreventAccessFromCentralDomains::class])
            ->group(base_path('routes/partner-panel.php'));
    }

    protected function centralDomains(): array
    {
        return config('tenancy.central_domains', []);
    }
}
