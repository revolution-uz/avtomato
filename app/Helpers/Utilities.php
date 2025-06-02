<?php

namespace App\Helpers;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Services\User\BalanceService;
use Carbon\Carbon;

class Utilities
{
    public const TENANT_SEPARATOR = '_';

    const MIN_MANUAL_PINFL_CARD_UPDATE_DAYS = 1;
    const LOYALTY_USER_MAX_SESSIONS = 1;
    const A_MONTH = 2592000;

    public static function getTimestampsOfMonthOfThisYear()
    {
        $currentYear = Carbon::now()->year;
        $timestamps = [];

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($currentYear, $month, 1, 0, 0, 0)->startOfMonth();
            $endOfMonth = Carbon::create($currentYear, $month, 1, 0, 0, 0)->endOfMonth();
            $timestamps[] = [
                'start' => $startOfMonth->timestamp,
                'end' => $endOfMonth->timestamp,
            ];
        }

        return $timestamps;
    }

    public static function getMonthNames()
    {
        $months = [];

        // Create an array of month numbers
        $monthNumbers = range(1, 12);

        foreach ($monthNumbers as $month) {
            // Create a Carbon instance for the first day of each month
            $date = Carbon::create(null, $month, 1);

            // Get month names in different languages
            $months[] = $date->locale(app()->getLocale())->monthName;
        }

        return $months;
    }

    public static function preventDivideByZero(float|int $numerator, float|int $denominator): float|int
    {
        return $numerator / ($denominator != 0 ? $denominator : 1);
    }

    public static function generateIdByTenancy(int|string $id): string
    {
        return tenant('id') . self::TENANT_SEPARATOR . $id;
    }

    public static function isAllowedUpdatePinflCards(?Carbon $searched_at): bool
    {
        $pinflCardUpdateDays = CompanySettings::getValue(CompanySettingsEnum::PINFL_CARDS_UPDATE_DAYS);

        if ($pinflCardUpdateDays == 0) {
            return false;
        }

        return is_null($searched_at) || $searched_at < Carbon::now()->subDays($pinflCardUpdateDays);
    }

    public static function isAllowedManualUpdatePinflCards(?Carbon $searched_at): bool
    {
        return is_null($searched_at) || $searched_at < Carbon::now()->subDays(self::MIN_MANUAL_PINFL_CARD_UPDATE_DAYS);
    }

    public static function isAllowedAutoCharge(FeatureEnum $provider): bool
    {
        return Feature::isFeatureActive(FeatureEnum::INSTALLMENT_AUTO_CHARGE)
            && Feature::isFeatureActive($provider)
            && app(BalanceService::class)->isAvailableCompany();
    }

    public static function isAllowedAutoChargeByPinfl(FeatureEnum $provider): bool
    {
        return Feature::isFeatureActive(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)
            && Feature::isFeatureActive($provider)
            && app(BalanceService::class)->isAvailableCompany();
    }
}
