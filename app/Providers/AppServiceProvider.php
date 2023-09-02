<?php

namespace App\Providers;

use App\Database\DatabaseConnector;
use App\Enums\Company\CompanyStatus;
use App\Helpers\UrlHelper;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
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
        if (!app()->runningInConsole() && UrlHelper::isHasCompanySlugInCurrentHost()) {
            /** @var Company|null $company */
            $company = Company::query()->where('subdomain', UrlHelper::getCompanySlugFromCurrentHost())->firstOrFail();

            if ($company !== null && $company->status === CompanyStatus::ACTIVE) {
                try {
                    DatabaseConnector::setCompanyConnection($company->id);
                } catch (\Exception $e) {
                    Log::error($e);

                    die($e->getMessage());
                }
            } else {
                die($company);
            }
        }
    }
}
