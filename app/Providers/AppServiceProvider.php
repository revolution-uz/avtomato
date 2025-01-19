<?php

namespace App\Providers;

use App\Enums\ApplicationEnvironmentEnum;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (ApplicationEnvironmentEnum::isProduction()) {
            URL::forceScheme('https');
        }
    }
}
