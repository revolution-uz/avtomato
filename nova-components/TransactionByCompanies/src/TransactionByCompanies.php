<?php

namespace Avtomato\TransactionByCompanies;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class TransactionByCompanies extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot(): void
    {
        Nova::mix('transaction-by-companies', __DIR__ . '/../dist/mix-manifest.json');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     */
    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Transaction By Companies')
            ->path('/transaction-by-companies')
            ->icon('banknotes');
    }
}
