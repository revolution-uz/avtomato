<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (str_contains(request()->url(), 'template')) {
            $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
            $verticalMenuData = json_decode($verticalMenuJson);
            $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
            $horizontalMenuData = json_decode($horizontalMenuJson);
        } else {
            $verticalMenuData = json_decode(json_encode([
                'menu' => [
                    ['name' => 'Statistika', 'icon' => 'menu-icon tf-icons ti ti-smart-home', 'slug' => 'dashboard', '/'],
                ],
            ]));
            $horizontalMenuData = json_decode(json_encode([
                'menu' => [
                    ['name' => 'Statistika', 'icon' => 'menu-icon tf-icons ti ti-smart-home', 'slug' => 'dashboard', '/'],
                ],
            ]));
        }

        // Share all menuData to all the views
        View::share('menuData', [$verticalMenuData, $horizontalMenuData]);
    }
}
