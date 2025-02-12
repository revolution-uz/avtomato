<?php

namespace App\Helpers;

use Carbon\Carbon;

class Utilities
{
    public const TENANT_SEPARATOR = '_';

    const PINFL_CARD_UPDATE_MONTHS = 3; //3 month to update cards
    const PINFL_CARD_UPDATE_REFRESH_DAYS = 5; //5 days to refresh cards

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

    public static function generateIdByTenancy(int $id): string
    {
        return tenant('id') . self::TENANT_SEPARATOR . $id;
    }

    public static function isNotAllowedAutoUpdateCards(Carbon $searched_at): bool
    {
        return $searched_at >= Carbon::now()->subMonths(self::PINFL_CARD_UPDATE_MONTHS);
    }

    public static function isNotAllowedRefresheCards(Carbon $searched_at): bool
    {
        return $searched_at >= Carbon::now()->subMonths(self::PINFL_CARD_UPDATE_REFRESH_DAYS);
    }
}
