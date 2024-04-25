<?php

namespace App\Enums;

use Carbon\Carbon;

enum FeatureEnum: string
{
    case PROFILE_EDIT = 'profile_edit';
    case USER_MANAGEMENT = 'user_management';
    case THEME_MANAGEMENT = 'theme_management';
    case CUSTOMER_MANAGEMENT = 'customer_management';
    case CUSTOMER_CARDS_MANAGEMENT = 'customer_cards_management';
    case GET_STATIC_INFORMATION = 'info_view';
    case CALCULATOR_INSTALLMENT = 'calculator_installment';
    case INSTALLMENT_MANAGEMENT = 'installment_management';
    case INSTALLMENT_SMS = 'installment_sms';
    case INSTALLMENT_AUTO_CHARGE = 'installment_auto_charge';
    case INSTALLMENT_GUARANTOR = 'installment_guarantor';
    case INSTALLMENT_COMMENT = 'installment_comment';
    case INSTALLMENT_DOCUMENT = 'installment_document';
    case INSTALLMENT_IMAGE = 'installment_image';
    case INSTALLMENT_AUTO_CHARGE_BY_PINFL = 'installment_auto_charge_by_pinfl';
    case TARIFF_MANAGEMENT = 'tariff_management';
    case TRANSACTION_MANAGEMENT = 'transaction_management';
    case TRANSACTION_TYPE_MANAGEMENT = 'transaction_type_management';
    case BRANCH_MANAGEMENT = 'branch_management';
    case ROLE_MANAGEMENT = 'role_management';
    case SCORING_CREDIT_HISTORY = 'scoring_credit_history';
    case SCORING_INPS_HISTORY = 'scoring_inps_history';
    case SCORING_PENSION_INFO = 'scoring_personal_info';
    case SCORING_MIB_INFO = 'scoring_mib_info';
    case SCORING_TAX_INFO = 'scoring_tax_info';
    case SCORING_HUMO = 'scoring_humo';
    case SCORING_UZCARD = 'scoring_uzcard';
    case ACTIVITY_LOG = 'activity_log';
    case ATMOS_OTP_PAY = 'atmos_otp_pay';
    case ATMOS_PINFL_PAY = 'atmos_pinfl_pay';

    public static function getData($case): array
    {
        if ($case == self::ATMOS_OTP_PAY) {
            return [
                'is_default_active' => false,
                'original_amount' => 0,
                'original_data' => [
                    'store_id' => config('clients.atmos.revolution.store_id'),
                    'username' => 'example',
                    'password' => 'example',
                    'token' => 'example',
                    'expires_in' => Carbon::now(),
                ],
            ];
        }
        if ($case == self::ATMOS_OTP_PAY) {
            return [
                'is_default_active' => false,
                'original_amount' => 0,
                'original_data' => [
                    'username' => 'example',
                    'password' => 'example',
                    'token' => 'example',
                    'expires_in' => Carbon::now(),
                ],
            ];
        }

        return [
            'is_default_active' => ModuleEnum::getModuleForFeature($case) === ModuleEnum::cases(),
            'original_amount' => 0,
        ];
    }
}
