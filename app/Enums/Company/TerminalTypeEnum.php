<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;
use App\Enums\FeatureEnum;

enum TerminalTypeEnum: int
{
    use EnumMethods;

    case ATMOS_CARD = 1;
    case ATMOS_PINFL = 2;
    case UNIRED_CARD = 3;
    case UNIRED_PINFL = 4;

    public function getDataFromFeature(): ?array
    {
        $featureEnum = match ($this) {
            self::ATMOS_CARD => FeatureEnum::ATMOS_CARD_PAY,
            self::ATMOS_PINFL => FeatureEnum::ATMOS_PINFL_PAY,
            self::UNIRED_CARD => FeatureEnum::UNIRED_CARD_PAY,
            self::UNIRED_PINFL => FeatureEnum::UNIRED_PINFL_PAY,
        };

        return $featureEnum->getFeature()->data;
    }
}
