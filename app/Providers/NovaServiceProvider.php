<?php

namespace App\Providers;

use App\Enums\Main\AdminRoleEnum;
use App\Helpers\UrlHelper;
use App\Models\Admin;
use App\Models\Company;
use App\Nova\AdminResource;
use App\Nova\Company\BranchResource;
use App\Nova\Company\FeatureResource;
use App\Nova\Company\LogResource;
use App\Nova\Company\RoleResource;
use App\Nova\Company\TariffsResource;
use App\Nova\Company\UserResource;
use App\Nova\CompanyResource;
use App\Nova\Dashboards\Main;
use App\Nova\SettingsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
                        MenuItem::resource(FeatureResource::class),
                        MenuItem::resource(BranchResource::class),
                        MenuItem::resource(TariffsResource::class),
                        MenuItem::resource(RoleResource::class),
                        MenuItem::resource(LogResource::class),
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
                    MenuItem::resource(SettingsResource::class),
                ])->collapsable(),
            ];
        });

        Nova::initialPath('/dashboards/main');

        Nova::userMenu(function (Request $request, Menu $menu) {
            /** @var Admin $admin */
            $admin = $request->user();

            if ($admin->role === AdminRoleEnum::SUPER_ADMIN) {
                $menu->append([
                    MenuItem::link('Horizon dashboard', '/horizon'),
                    MenuItem::externalLink('Log viewer', '/nova/log-viewer')->openInNewTab(),
                ]
                );
            }

            return $menu;
        });

        Nova::withoutNotificationCenter();
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
     *
     * @return void
     */
    public function register()
    {
        //
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
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
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
