<?php

namespace App\Enums;

use App\Enums\Company\LimitAdminEnum;
use App\Enums\Company\LimitBranchEnum;
use Exception;

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
    case ECOMMERCE = 'ecommerce';
    case SBP_PAY = 'sbp_pay';

    /**
     * @return ModuleEnum[]
     */
    public static function getModuleForFeature(FeatureEnum $feature): array
    {
        return match ($feature) {
            FeatureEnum::USER_MANAGEMENT,
            FeatureEnum::BRANCH_MANAGEMENT,
            FeatureEnum::ACTIVITY_LOG,
            FeatureEnum::ROLE_MANAGEMENT => [self::MULTIBRANCH_2, self::MULTIBRANCH_3, self::MULTIBRANCH_4, self::ECOMMERCE],
            FeatureEnum::SMS => [self::INSTALLMENT, self::MARKETING],
            FeatureEnum::CUSTOMER_MANAGEMENT => [self::INSTALLMENT, self::SCORING],
            FeatureEnum::ECOMMERCE => [self::ECOMMERCE],
            FeatureEnum::SBP_PAY => [self::SBP_PAY],
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
            FeatureEnum::UNIRED_PAY,
            FeatureEnum::ATMOS_CARD_PAY,
            FeatureEnum::ATMOS_PINFL_PAY => [self::INSTALLMENT],
            FeatureEnum::SCORING_CREDIT_HISTORY,
            FeatureEnum::SCORING_INPS_HISTORY,
            FeatureEnum::SCORING_PENSION_INFO,
            FeatureEnum::SCORING_MIB_INFO,
            FeatureEnum::SCORING_TAX_INFO,
            FeatureEnum::SCORING_HUMO,
            FeatureEnum::SCORING_UZCARD,
            FeatureEnum::SCORING_ROYXAT_CREDIT_HISTORY,
            FeatureEnum::SCORING_UZUM_NASIYA_LIMIT => [self::SCORING],
            FeatureEnum::PROFILE_EDIT,
            FeatureEnum::BALANCE_FUND,
            FeatureEnum::BALANCE_CHARGE,
            FeatureEnum::THEME_MANAGEMENT,
            FeatureEnum::GENERAL_FEATURE,
            FeatureEnum::COMPANY_MANAGEMENT => self::cases(),
        };
    }

    /**
     * @throws Exception
     */
    public static function getAdminsLimit(ModuleEnum $module): int
    {
        return match ($module) {
            self::MULTIBRANCH_1 => LimitAdminEnum::MULTIBRANCH_1_ADMIN->value,
            self::MULTIBRANCH_2 => LimitAdminEnum::MULTIBRANCH_2_ADMIN->value,
            self::MULTIBRANCH_3 => LimitAdminEnum::MULTIBRANCH_3_ADMIN->value,
            self::MULTIBRANCH_4 => LimitAdminEnum::MULTIBRANCH_4_ADMIN->value,
            self::SBP_PAY, self::ECOMMERCE, self::SCORING, self::MARKETING, self::INSTALLMENT => throw new Exception('Is not limited'),
        };
    }

    /**
     * @throws Exception
     */
    public static function getBranchLimit(ModuleEnum $module): int
    {
        return match ($module) {
            self::MULTIBRANCH_1 => LimitBranchEnum::MULTIBRANCH_1_BRANCH->value,
            self::MULTIBRANCH_2 => LimitBranchEnum::MULTIBRANCH_2_BRANCH->value,
            self::MULTIBRANCH_3 => LimitBranchEnum::MULTIBRANCH_3_BRANCH->value,
            self::MULTIBRANCH_4 => LimitBranchEnum::MULTIBRANCH_4_BRANCH->value,
            self::SBP_PAY, self::ECOMMERCE, self::SCORING, self::INSTALLMENT, self::MARKETING => throw new Exception('Is not limited'),
        };
    }
}
