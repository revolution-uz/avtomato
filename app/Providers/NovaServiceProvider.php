<?php

namespace App\Providers;

use App\Enums\Main\AdminRole;
use App\Helpers\UrlHelper;
use App\Models\Company;
use App\Nova\AdminResource;
use App\Nova\Company\FeaturesResource;
use App\Nova\Company\TariffsResource;
use App\Nova\Company\UserResource;
use App\Nova\CompanyResource;
use App\Nova\Dashboards\Main;
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
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        if (!app()->runningInConsole() && UrlHelper::isHasCompanySlugInCurrentHost()) {
            Nova::mainMenu(function (Request $request, Menu $menu) {
                /** @var Company $company */
                $company = Company::query()
                    ->where('subdomain', UrlHelper::getCompanySlugFromCurrentHost())
                    ->firstOrFail();

                return [
                    MenuItem::externalLink('BACK TO GLOBAL PANEL', UrlHelper::getAdminUrl())->openInNewTab(),
                    MenuItem::link($company->name, '/'),
                    MenuSection::make('Dashboards', [
                        MenuItem::dashboard(Main::class),
                        // here will be another statistic dashboards
                    ])->collapsable()->icon('view-grid'),
                    MenuSection::make('Resources', [
                        MenuItem::resource(UserResource::class),
                        MenuItem::resource(FeaturesResource::class),
                        MenuItem::resource(TariffsResource::class),
                    ])->collapsable(),
                ];
            });

            Nova::initialPath('/dashboards/main');
        } else {
            Nova::mainMenu(function (Request $request, Menu $menu) {
                return [
                    MenuSection::make('Dashboards', [
                        MenuItem::dashboard(Main::class),
                        // here will be another statistic dashboards
                    ])->collapsable()->icon('view-grid'),
                    MenuSection::make('Resources', [
                        MenuItem::resource(CompanyResource::class),
                        MenuItem::resource(AdminResource::class),
                    ])->collapsable(),
                ];
            });

            Nova::initialPath('/dashboards/main');
        }

        Nova::userMenu(function (Request $request, Menu $menu) {
            if ($request->user()->role === AdminRole::SUPER_ADMIN) {
                $menu->prepend(
                    MenuItem::link('Horizon dashboard', '/horizon')
                );
            }

            return $menu;
        });

        Nova::withoutNotificationCenter();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return array
     */
    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
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
}
