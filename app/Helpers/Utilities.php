<?php

namespace App\Helpers;

use Carbon\Carbon;

class Utilities
{
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
}
