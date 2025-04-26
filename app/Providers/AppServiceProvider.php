<?php

namespace App\Providers;

use App\Enums\ApplicationEnvironmentEnum;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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

        // Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
