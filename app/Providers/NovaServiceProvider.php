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
use App\Nova\BlogCategoryResource;
use App\Nova\BlogResource;
use App\Nova\CentralBalanceFundResource;
use App\Nova\CentralCustomerResource;
use App\Nova\CentralUniredCardResource;
use App\Nova\Company\BalanceChargeResource;
use App\Nova\Company\BalanceFundResource;
use App\Nova\Company\BranchResource;
use App\Nova\Company\CompanyAdminResource;
use App\Nova\Company\CompanySettingsResource;
use App\Nova\Company\FeatureResource;
use App\Nova\Company\InstallmentResource;
use App\Nova\Company\LoyaltyProgram\ItemResource;
use App\Nova\Company\LoyaltyProgram\OnboardingImageResource;
use App\Nova\Company\LoyaltyProgram\PriceResource;
use App\Nova\Company\LoyaltyProgram\ShopResource;
use App\Nova\Company\LoyaltyProgram\StockResource;
use App\Nova\Company\LoyaltyProgram\SubscriptionResource;
use App\Nova\Company\ModuleResource;
use App\Nova\Company\RoleResource;
use App\Nova\Company\SbpTransactionResource;
use App\Nova\Company\SmsTemplateResource;
use App\Nova\CompanyResource;
use App\Nova\Dashboards\Main;
use App\Nova\NewCustomerResource;
use App\Nova\SbpAccountResource;
use App\Nova\SettingsResource;
use Avtomato\ConsoleCommands\ConsoleCommands;
use Avtomato\TransactionByCompanies\TransactionByCompanies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Features;
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
                        MenuItem::resource(CompanyAdminResource::class),
                        MenuItem::resource(ModuleResource::class),
                        MenuItem::resource(FeatureResource::class),
                        MenuItem::resource(CompanySettingsResource::class),
                        MenuItem::resource(BranchResource::class),
                        MenuItem::resource(RoleResource::class),
                        MenuItem::resource(BalanceFundResource::class),
                        MenuItem::resource(BalanceChargeResource::class),
                        MenuItem::resource(SmsTemplateResource::class),
                        MenuItem::resource(InstallmentResource::class),
                        MenuItem::resource(SbpTransactionResource::class),
                    ])->collapsable(),
                    MenuSection::make('Loyalty Program', [
                        MenuItem::resource(\App\Nova\Company\LoyaltyProgram\UserResource::class),
                        MenuItem::resource(SubscriptionResource::class),
                        MenuItem::resource(ShopResource::class),
                        MenuItem::resource(ItemResource::class),
                        MenuItem::resource(PriceResource::class),
                        MenuItem::resource(StockResource::class),
                        MenuItem::resource(OnboardingImageResource::class),
                    ])->collapsable(),
                ];
            }

            return [
                MenuSection::make('Dashboards', [
                    MenuItem::dashboard(Main::class),
                    // here will be another statistic dashboards
                ])->collapsable()->icon('view-grid'),
                (new ConsoleCommands)->menu($request),
                MenuSection::make('Resources', [
                    MenuItem::resource(CompanyResource::class),
                    MenuItem::resource(AdminResource::class),
                    MenuItem::resource(CentralCustomerResource::class),
                    MenuItem::resource(SbpAccountResource::class),
                    MenuItem::resource(SettingsResource::class),
                    MenuItem::resource(BaseModuleResource::class),
                    MenuItem::resource(BaseFeatureResource::class),
                    MenuItem::resource(BaseSmsTemplateResource::class),
                    MenuItem::resource(NewCustomerResource::class),
                    MenuItem::resource(BlogResource::class),
                    MenuItem::resource(BlogCategoryResource::class),
                    MenuItem::resource(CentralBalanceFundResource::class),
                    MenuItem::resource(CentralUniredCardResource::class),
                    (new TransactionByCompanies)->menu($request),
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
                ]);
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
        return [
            new TransactionByCompanies,
            new ConsoleCommands,
        ];
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
     * Register the configurations for Laravel Fortify.
     */
    protected function fortify(): void
    {
        Nova::fortify()
            ->features([
                Features::updatePasswords(),
                // Features::emailVerification(),
                // Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
            ])
            ->register();
    }

    /**
     * Register the Nova routes.
     */
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes(default: true)
            ->withPasswordResetRoutes()
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
