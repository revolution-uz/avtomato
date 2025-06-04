<?php

namespace App\Enums\Company\Scoring;

use App\Enums\External\AsaxiyKatmTypeEnum;
use App\Enums\FeatureEnum;
use InvalidArgumentException;

enum ScoringRequestTypeEnum: int
{
    case KATM_CREDIT_HISTORY = 1;
    case KATM_INPS_HISTORY = 2;
    case KATM_PENSION_INFO = 3;
    case KATM_START_INFO = 10;
    case PLUM_MIB_INFO = 4;
    case PLUM_TAX_INFO = 5;
    case HUMO = 6;
    case UZCARD = 7;
    case ROYXAT_CREDIT_HISTORY = 8;
    case UZUM_NASIYA_LIMIT = 9;

    public static function cardTypes(): array
    {
        return [
            self::HUMO,
            self::UZCARD,
        ];
    }

    public function convertToAsaxiyFormat(): AsaxiyKatmTypeEnum
    {
        return match ($this) {
            self::KATM_CREDIT_HISTORY => AsaxiyKatmTypeEnum::KATM_CREDIT_HISTORY,
            self::KATM_INPS_HISTORY => AsaxiyKatmTypeEnum::KATM_INPS_HISTORY,
            self::KATM_PENSION_INFO => AsaxiyKatmTypeEnum::KATM_PENSION_INFO,
            self::KATM_START_INFO => AsaxiyKatmTypeEnum::KATM_START_INFO,
            default => throw new InvalidArgumentException,
        };
    }

    public function getFeatureEnum(): FeatureEnum
    {
        return match ($this) {
            self::KATM_CREDIT_HISTORY => FeatureEnum::SCORING_CREDIT_HISTORY,
            self::KATM_INPS_HISTORY => FeatureEnum::SCORING_INPS_HISTORY,
            self::KATM_PENSION_INFO => FeatureEnum::SCORING_PENSION_INFO,
            self::KATM_START_INFO => FeatureEnum::SCORING_START_INFO,
            default => throw new InvalidArgumentException,
        };
    }
}
