<?php

namespace App\Enums\Company\LoyaltyProgram;

use App\Enums\EnumMethods;

enum LoyaltyProgramStatisticsChartEnum: int
{
    use EnumMethods;

    case USERS_CHART = 0;
    case SUBSCRIBED_USERS_CHART = 1;
    case EXTENDED_SUBSCRIPTION_USERS_CHART = 2;
    case NON_EXTENDED_SUBSCRIPTION_USERS_CHART = 3;
}
