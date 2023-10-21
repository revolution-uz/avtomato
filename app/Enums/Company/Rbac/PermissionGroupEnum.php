<?php

namespace App\Enums\Company\Rbac;

enum PermissionGroupEnum: string
{
    case PROFILE_EDIT = 'profile_edit';
    case USER_MANAGEMENT = 'user_management';
    case THEME_MANAGEMENT = 'theme_management';
    case CUSTOMER_MANAGEMENT = 'customer_management';
    case CUSTOMER_CARDS_MANAGEMENT = 'customer_cards_management';
    case GET_STATIC_INFORMATION = 'info_view';
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
}
