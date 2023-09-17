<?php

namespace App\Helpers;

use App\DTOs\Common\MenuItem;
use App\Enums\Company\FeaturesEnum;
use App\Models\Company\Features;

class MenuHelper
{
    /**
     * @return MenuItem[]
     */
    public static function buildMenuItems(): array
    {
        $menuItems = [];
        $menuItems[] = new MenuItem(
            name: 'Statistika',
            icon: 'menu-icon tf-icons ti ti-smart-home',
            url: localized_route('dashboard'),
        );

        if (Features::isFeatureActive(FeaturesEnum::MULTI_BRANCH)) {
            $menuItems[] = new MenuItem(
                name: 'Filiallar',
                icon: 'menu-icon tf-icons ti ti-smart-home',
                url: localized_route('branch'),
            );
        }

        // to test submenu and divider
        $menuItems[] = new MenuItem(
            menuHeader: 'Test uchun menyular'
        );
        $menuItems[] = new MenuItem(
            name: 'Layoutlar',
            icon: 'menu-icon tf-icons ti ti-layout-sidebar',
            url: ['/layouts/horizontal', '/layouts/vertical'],
            // or you can `url: 'layout'` and it will be active when URL is layout/*
            subMenu: [
                new MenuItem(
                    name: 'Horizontal',
                    url: '/layouts/horizontal',
                ),
                new MenuItem(
                    name: 'Vertikal',
                    url: '/layouts/vertical',
                ),
                new MenuItem(
                    name: 'Boshqalar',
                    subMenu: [
                        new MenuItem(
                            name: 'Blank',
                            url: '/layouts/blank',
                        ),
                        new MenuItem(
                            name: 'Navbar full',
                            url: '/layouts/navbar-full',
                        ),
                    ],
                ),
            ],
        );
        $menuItems[] = new MenuItem(
            name: 'Tarifflar',
            icon: 'menu-icon tf-icons ti ti-brand-tumblr',
            url: '/tariffs',
        );

        return $menuItems;
    }
}
