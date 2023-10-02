<?php

namespace App\Enums\Company\Scoring;

use App\Enums\External\AsaxiyKatmTypeEnum;

enum ScoringRequestTypeEnum: int
{
    case KATM_CREDIT_HISTORY = 1;
    case KATM_INPS_HISTORY = 2;
    case KATM_PENSION_INFO = 3;

    public function convertToAsaxiyFormat(): AsaxiyKatmTypeEnum
    {
        return match ($this) {
            self::KATM_CREDIT_HISTORY => AsaxiyKatmTypeEnum::KATM_CREDIT_HISTORY,
            self::KATM_INPS_HISTORY => AsaxiyKatmTypeEnum::KATM_INPS_HISTORY,
            self::KATM_PENSION_INFO => AsaxiyKatmTypeEnum::KATM_PENSION_INFO,
        };
    }
}
