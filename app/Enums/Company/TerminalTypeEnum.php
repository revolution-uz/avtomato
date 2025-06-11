<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;

enum TerminalTypeEnum: int
{
    use EnumMethods;

    case ATMOS_CARD = 1;
    case ATMOS_PINFL = 2;
    case UNIRED_CARD = 3;
    case UNIRED_PINFL = 4;

    public static function options(): array
    {
        return [
            self::ATMOS_CARD->value => 'Atmos card',
            self::ATMOS_PINFL->value => 'Atmos pinfl',
            self::UNIRED_CARD->value => 'Unired card',
            self::UNIRED_PINFL->value => 'Unired pinfl',
        ];
    }

    public function getDataFromFeature(): ?array
    {
        $featureEnum = match ($this) {
            self::ATMOS_CARD => FeatureEnum::ATMOS_CARD_PAY,
            self::ATMOS_PINFL => FeatureEnum::ATMOS_PINFL_PAY,
            self::UNIRED_CARD => FeatureEnum::UNIRED_CARD_PAY,
            self::UNIRED_PINFL => FeatureEnum::UNIRED_PINFL_PAY,
        };

        return Feature::findByName($featureEnum)->data;
    }
}
