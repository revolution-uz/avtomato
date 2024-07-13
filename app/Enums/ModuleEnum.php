<?php

namespace App\Enums;

enum ModuleEnum: string
{
    use EnumMethods;

    case SCORING = 'scoring';
    case INSTALLMENT = 'installment';
    case MARKETING = 'marketing';
    case MULTIBRANCH_1 = 'multibranch_1';
    case MULTIBRANCH_2 = 'multibranch_2';
    case MULTIBRANCH_3 = 'multibranch_3';
    case MULTIBRANCH_4 = 'multibranch_4';

    /**
     * @return ModuleEnum[]
     */
    public static function getModuleForFeature(FeatureEnum $feature): array
    {
        return match ($feature) {
            FeatureEnum::USER_MANAGEMENT,
            FeatureEnum::BRANCH_MANAGEMENT,
            FeatureEnum::ACTIVITY_LOG,
            FeatureEnum::ROLE_MANAGEMENT => [self::MULTIBRANCH_2, self::MULTIBRANCH_3, self::MULTIBRANCH_4],
            FeatureEnum::SMS => [self::INSTALLMENT, self::MARKETING],
            FeatureEnum::CUSTOMER_MANAGEMENT => [self::INSTALLMENT, self::SCORING],
            FeatureEnum::CUSTOMER_CARDS_MANAGEMENT,
            FeatureEnum::CALCULATOR_INSTALLMENT,
            FeatureEnum::INSTALLMENT_AUTO_CHARGE,
            FeatureEnum::INSTALLMENT_GUARANTOR,
            FeatureEnum::INSTALLMENT_COMMENT,
            FeatureEnum::INSTALLMENT_DOCUMENT,
            FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL,
            FeatureEnum::TARIFF_MANAGEMENT,
            FeatureEnum::TRANSACTION_MANAGEMENT,
            FeatureEnum::TRANSACTION_TYPE_MANAGEMENT,
            FeatureEnum::INSTALLMENT_MANAGEMENT,
            FeatureEnum::ATMOS_CARD_PAY,
            FeatureEnum::ATMOS_PINFL_PAY => [self::INSTALLMENT],
            FeatureEnum::SCORING_CREDIT_HISTORY,
            FeatureEnum::SCORING_INPS_HISTORY,
            FeatureEnum::SCORING_PENSION_INFO,
            FeatureEnum::SCORING_MIB_INFO,
            FeatureEnum::SCORING_TAX_INFO,
            FeatureEnum::SCORING_HUMO,
            FeatureEnum::SCORING_UZCARD => [self::SCORING],
            FeatureEnum::PROFILE_EDIT,
            FeatureEnum::BALANCE_FUND,
            FeatureEnum::BALANCE_CHARGE,
            FeatureEnum::THEME_MANAGEMENT => self::cases(),
        };
    }
}
