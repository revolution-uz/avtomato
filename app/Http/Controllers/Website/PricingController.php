<?php

namespace App\Http\Controllers\Website;

use App\Models\BaseModule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class PricingController
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $baseModules = BaseModule::all();

        $moduleNames = [
            'installment' => 'Nasiya savdo moduli',
            'scoring' => 'Skoring moduli',
            'marketing' => 'Marketing moduli',
            'ecommerce' => 'Internet do’kon moduli',
            'sbp_pay' => 'QR pay moduli',
            'loyalty_program' => 'Loyalty moduli',
        ];

        $filteredModules = [];
        $multibranchModule = null;
        $hasMultibranch = false; // Флаг наличия multibranch_*

        foreach ($baseModules as $module) {
            if (str_starts_with($module['name']->value, 'multibranch_')) {
                $multibranchModule = $module; // Сохраняем multibranch-модуль
                $hasMultibranch = true;
            } else {
                $filteredModules[] = $module;
            }
        }

        return view('website.pages.pricing', ['baseModules' => $baseModules, 'moduleNames' => $moduleNames, 'filteredModules' => $filteredModules, 'multibranchModule' => $multibranchModule, 'hasMultibranch' => $hasMultibranch]);
    }
}
