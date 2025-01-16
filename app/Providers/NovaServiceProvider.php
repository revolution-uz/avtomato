<?php

namespace App\Providers;

use App\Enums\Main\AdminRoleEnum;
use App\Helpers\UrlHelper;
use App\Models\Admin;
use App\Models\Company;
use App\Nova\AdminResource;
use App\Nova\BaseFeatureResource;
use App\Nova\BaseModuleResource;
use App\Nova\BaseSmsTemplateResource;
use App\Nova\Company\BalanceChargeResource;
use App\Nova\Company\BalanceFundResource;
use App\Nova\Company\BranchResource;
use App\Nova\Company\FeatureResource;
use App\Nova\Company\ModuleResource;
use App\Nova\Company\RoleResource;
use App\Nova\Company\SmsTemplateResource;
use App\Nova\Company\UserResource;
use App\Nova\CompanyResource;
use App\Nova\CustomerResource;
use App\Nova\Dashboards\Main;
use App\Nova\SettingsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Menu\Menu;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
        Nova::mainMenu(function (Request $request, Menu $menu) {
            if (tenancy()->initialized) {
                /** @var Company $company */
                $company = tenancy()->tenant;

                return [
                    MenuItem::externalLink('BACK TO GLOBAL PANEL', UrlHelper::getAdminUrl())->openInNewTab(),
                    MenuItem::link($company->name, '/'),
                    MenuSection::make('Dashboards', [
                        MenuItem::dashboard(Main::class),
                        // here will be another statistic dashboards
                    ])->collapsable()->icon('view-grid'),
                    MenuSection::make('Resources', [
                        MenuItem::resource(UserResource::class),
                        MenuItem::resource(ModuleResource::class),
                        MenuItem::resource(FeatureResource::class),
                        MenuItem::resource(BranchResource::class),
                        MenuItem::resource(RoleResource::class),
                        MenuItem::resource(BalanceFundResource::class),
                        MenuItem::resource(BalanceChargeResource::class),
                        MenuItem::resource(SmsTemplateResource::class),
                    ])->collapsable(),
                ];
            }

            return [
                MenuSection::make('Dashboards', [
                    MenuItem::dashboard(Main::class),
                    // here will be another statistic dashboards
                ])->collapsable()->icon('view-grid'),
                MenuSection::make('Resources', [
                    MenuItem::resource(CompanyResource::class),
                    MenuItem::resource(AdminResource::class),
                    MenuItem::resource(CustomerResource::class),
                    MenuItem::resource(SettingsResource::class),
                    MenuItem::resource(BaseModuleResource::class),
                    MenuItem::resource(BaseFeatureResource::class),
                    MenuItem::resource(BaseSmsTemplateResource::class),
                ])->collapsable(),
            ];
        });

        Nova::initialPath('/dashboards/main');

        Nova::userMenu(function (Request $request, Menu $menu) {
            /** @var Admin|null $admin */
            $admin = $request->user();

            if ($admin && $admin->role === AdminRoleEnum::SUPER_ADMIN) {
                $menu->append([
                    MenuItem::externalLink('Horizon dashboard', '/nova/horizon')->openInNewTab(),
                    MenuItem::externalLink('Log viewer', '/nova/log-viewer')->openInNewTab(),
                ]
                );
            }

            return $menu;
        });

        Nova::withoutNotificationCenter();
        Nova::withBreadcrumbs();
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     */
    public function tools(): array
    {
        return [];
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        parent::register();

        Nova::report(
            function ($exception) {
                Log::error($exception);
            }
        );
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewNova', function ($admin) {
            return (bool) $admin;
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     */
    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }
}
