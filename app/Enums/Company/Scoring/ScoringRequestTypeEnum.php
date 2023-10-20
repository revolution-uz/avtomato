<?php

namespace App\Enums\Company\Scoring;

use App\Enums\External\AsaxiyKatmTypeEnum;
use InvalidArgumentException;

enum ScoringRequestTypeEnum: int
{
    case KATM_CREDIT_HISTORY = 1;
    case KATM_INPS_HISTORY = 2;
    case KATM_PENSION_INFO = 3;
    case PLUM_MIB_INFO = 4;
    case PLUM_TAX_INFO = 5;
    case HUMO = 6;
    case UZKARD = 7;

    public function convertToAsaxiyFormat(): AsaxiyKatmTypeEnum
    {
        return match ($this) {
            self::KATM_CREDIT_HISTORY => AsaxiyKatmTypeEnum::KATM_CREDIT_HISTORY,
            self::KATM_INPS_HISTORY => AsaxiyKatmTypeEnum::KATM_INPS_HISTORY,
            self::KATM_PENSION_INFO => AsaxiyKatmTypeEnum::KATM_PENSION_INFO,
            default => throw new InvalidArgumentException,
        };
    }
}
