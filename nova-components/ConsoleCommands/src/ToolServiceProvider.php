<?php

namespace Avtomato\ConsoleCommands;

use Avtomato\ConsoleCommands\Http\Middleware\Authorize;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Register the tool's routes.
     */
    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', 'nova.auth', Authorize::class], 'console-commands')
            ->group(__DIR__ . '/../routes/inertia.php');

        Route::middleware(['nova', 'nova.auth', Authorize::class])
            ->prefix('nova-vendor/console-commands')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
