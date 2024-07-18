<?php

namespace App\Enums\Company\Rbac;

use App\Enums\EnumMethods;

enum PermissionGroupEnum: string
{
    use EnumMethods;

    case PROFILE_EDIT = 'profile_edit';
    case BALANCE_FUND = 'balance_fund';
    case BALANCE_CHARGE = 'balance_charge';
    case ADMIN_MANAGEMENT = 'admin_management';
    case CUSTOMER_MANAGEMENT = 'customer_management';
    case CALCULATOR_INSTALLMENT = 'calculator_installment';
    case INSTALLMENT_MANAGEMENT = 'installment_management';
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

    /**
     * @return PermissionEnum[]
     */
    public static function getPermissionsByGroup(self $group): array
    {
        return match ($group) {
            self::ADMIN_MANAGEMENT => [
                PermissionEnum::ADMIN_VIEW_ALL,
                PermissionEnum::ADMIN_VIEW,
                PermissionEnum::ADMIN_CREATE,
                PermissionEnum::ADMIN_EDIT,
                PermissionEnum::ADMIN_DELETE,
                PermissionEnum::ADMIN_CHANGE_PASSWORD,
                PermissionEnum::ADMIN_CHANGE_AVATAR,
                PermissionEnum::ADMIN_CHANGE_ROLE,
                PermissionEnum::ADMIN_DEVICES,
                PermissionEnum::ADMIN_REVOKE_DEVICE,
                PermissionEnum::ADMIN_CHANGE_THEME,
            ],
            self::CUSTOMER_MANAGEMENT => [
                PermissionEnum::CUSTOMER_VIEW_ALL,
                PermissionEnum::CUSTOMER_VIEW,
                PermissionEnum::CUSTOMER_VIEW_INSTALLMENTS,
                PermissionEnum::CUSTOMER_CREATE,
                PermissionEnum::CUSTOMER_INFO_BY_PASSPORT,
                PermissionEnum::CUSTOMER_EDIT,
                PermissionEnum::CUSTOMER_CARD_VIEW,
                PermissionEnum::CUSTOMER_CARD_EDIT,
                PermissionEnum::CUSTOMER_CARD_REMOVE,
            ],
            self::ROLE_MANAGEMENT => [
                PermissionEnum::ROLE_VIEW_ALL,
                PermissionEnum::ROLE_VIEW,
                PermissionEnum::ROLE_CREATE,
                PermissionEnum::ROLE_EDIT,
                PermissionEnum::ROLE_DELETE,
                PermissionEnum::ROLE_PERMISSIONS,
            ],
            self::CALCULATOR_INSTALLMENT => [
                PermissionEnum::CALCULATE_INSTALLMENT,
                PermissionEnum::CALCULATOR_EXPORT,
            ],
            self::INSTALLMENT_MANAGEMENT => [
                PermissionEnum::INSTALLMENT_VIEW_ALL,
                PermissionEnum::INSTALLMENT_VIEW,
                PermissionEnum::INSTALLMENT_CREATE,
                PermissionEnum::INSTALLMENT_START,
                PermissionEnum::INSTALLMENT_CANCEL,
                PermissionEnum::INSTALLMENT_FREEZE,
                PermissionEnum::INSTALLMENT_FINISH,
                PermissionEnum::INSTALLMENT_EDIT,
                PermissionEnum::INSTALLMENT_DELETE,
                PermissionEnum::INSTALLMENT_ATTACH_CARD,
                PermissionEnum::INSTALLMENT_DETACH_CARD,
                PermissionEnum::INSTALLMENT_CUSTOMER_LIST,
                PermissionEnum::INSTALLMENT_ATTACH_GUARANTOR,
                PermissionEnum::INSTALLMENT_UPDATE_GUARANTOR,
                PermissionEnum::INSTALLMENT_DETACH_GUARANTOR,
                PermissionEnum::INSTALLMENT_CREATE_COMMENT,
                PermissionEnum::INSTALLMENT_UPDATE_COMMENT,
                PermissionEnum::INSTALLMENT_DELETE_COMMENT,
                PermissionEnum::INSTALLMENT_UPLOAD_DOCUMENT,
                PermissionEnum::INSTALLMENT_DELETE_DOCUMENT,
                PermissionEnum::INSTALLMENT_CREATE_TRANSACTION,
                PermissionEnum::DASHBOARD_INFO_VIEW,
            ],
            self::TARIFF_MANAGEMENT => [
                PermissionEnum::TARIFF_VIEW_ALL,
                PermissionEnum::TARIFF_VIEW,
                PermissionEnum::TARIFF_EDIT,
                PermissionEnum::TARIFF_DELETE,
                PermissionEnum::TARIFF_CREATE,
            ],
            self::TRANSACTION_MANAGEMENT => [
                PermissionEnum::TRANSACTION_ATTEMPT_VIEW_ALL,
                PermissionEnum::TRANSACTION_VIEW_ALL,
                PermissionEnum::TRANSACTION_VIEW,
                PermissionEnum::TRANSACTION_CREATE,
                PermissionEnum::TRANSACTION_EDIT,
                PermissionEnum::TRANSACTION_REVERSE,
                PermissionEnum::TRANSACTION_DELETE,
            ],
            self::TRANSACTION_TYPE_MANAGEMENT => [
                PermissionEnum::TRANSACTION_TYPE_VIEW_ALL,
                PermissionEnum::TRANSACTION_TYPE_VIEW,
                PermissionEnum::TRANSACTION_TYPE_CREATE,
                PermissionEnum::TRANSACTION_TYPE_EDIT,
                PermissionEnum::TRANSACTION_TYPE_DELETE,
            ],
            self::BRANCH_MANAGEMENT => [
                PermissionEnum::BRANCH_VIEW_ALL,
                PermissionEnum::BRANCH_VIEW,
                PermissionEnum::BRANCH_EDIT,
                PermissionEnum::BRANCH_DELETE,
                PermissionEnum::BRANCH_CREATE,
            ],
            self::SCORING_CREDIT_HISTORY => [
                PermissionEnum::SCORING_CREDIT_HISTORY_VIEW_ALL,
                PermissionEnum::SCORING_CREDIT_HISTORY_VIEW,
                PermissionEnum::SCORING_CREDIT_HISTORY_CREATE,
                PermissionEnum::SCORING_CREDIT_HISTORY_DELETE,
            ],
            self::SCORING_INPS_HISTORY => [
                PermissionEnum::SCORING_INPS_HISTORY_VIEW_ALL,
                PermissionEnum::SCORING_INPS_HISTORY_VIEW,
                PermissionEnum::SCORING_INPS_HISTORY_CREATE,
                PermissionEnum::SCORING_INPS_HISTORY_DELETE,
            ],
            self::SCORING_PENSION_INFO => [
                PermissionEnum::SCORING_PENSION_INFO_VIEW_ALL,
                PermissionEnum::SCORING_PENSION_INFO_VIEW,
                PermissionEnum::SCORING_PENSION_INFO_CREATE,
                PermissionEnum::SCORING_PENSION_INFO_DELETE,
            ],
            self::SCORING_MIB_INFO => [
                PermissionEnum::SCORING_MIB_INFO_VIEW_ALL,
                PermissionEnum::SCORING_MIB_INFO_VIEW,
                PermissionEnum::SCORING_MIB_INFO_CREATE,
                PermissionEnum::SCORING_MIB_INFO_DELETE,
            ],
            self::SCORING_TAX_INFO => [
                PermissionEnum::SCORING_TAX_INFO_VIEW_ALL,
                PermissionEnum::SCORING_TAX_INFO_VIEW,
                PermissionEnum::SCORING_TAX_INFO_CREATE,
                PermissionEnum::SCORING_TAX_INFO_DELETE,
            ],
            self::SCORING_HUMO => [
                PermissionEnum::SCORING_HUMO_VIEW_ALL,
                PermissionEnum::SCORING_HUMO_VIEW,
                PermissionEnum::SCORING_HUMO_CREATE,
                PermissionEnum::SCORING_HUMO_DELETE,
            ],
            self::SCORING_UZCARD => [
                PermissionEnum::SCORING_UZCARD_VIEW_ALL,
                PermissionEnum::SCORING_UZCARD_VIEW,
                PermissionEnum::SCORING_UZCARD_CREATE,
                PermissionEnum::SCORING_UZCARD_DELETE,
            ],
            self::PROFILE_EDIT => [
                PermissionEnum::PROFILE_EDIT,
                PermissionEnum::PROFILE_CHANGE_PASSWORD,
                PermissionEnum::PROFILE_CHANGE_LANGUAGE,
                PermissionEnum::PROFILE_CHANGE_AVATAR,
                PermissionEnum::PROFILE_DEVICES,
                PermissionEnum::PROFILE_REVOKE_DEVICE,
                PermissionEnum::PROFILE_CHANGE_THEME,
            ],
            self::BALANCE_FUND => [
                PermissionEnum::BALANCE_FUND_VIEW_ALL,
                PermissionEnum::BALANCE_FUND_VIEW,
                PermissionEnum::BALANCE_FUND_CONFIRM,
                PermissionEnum::BALANCE_FUND_DELETE,
                PermissionEnum::BALANCE_FUND_CREATE,
            ],
            self::BALANCE_CHARGE => [
                PermissionEnum::BALANCE_CHARGE_VIEW_ALL,
                PermissionEnum::BALANCE_CHARGE_VIEW,
                PermissionEnum::BALANCE_CHARGE_CONFIRM,
                PermissionEnum::BALANCE_CHARGE_DELETE,
                PermissionEnum::BALANCE_CHARGE_CREATE,
            ],
            self::ACTIVITY_LOG => [
                PermissionEnum::ACTIVITY_LOG_VIEW,
                PermissionEnum::ACTIVITY_LOG_VIEW_ALL,
                PermissionEnum::ACTIVITY_LOG_VIEW_OWN,
            ],
        };
    }
}
