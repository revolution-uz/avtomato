<?php

namespace App\Enums\Company\Rbac;

enum PermissionGroupEnum: string
{
    case PROFILE_EDIT = 'profile_edit';
    case USER_MANAGEMENT = 'user_management';
    case THEME_MANAGEMENT = 'theme_management';
    case CUSTOMER_MANAGEMENT = 'customer_management';
    case GET_STATIC_INFORMATION = 'info_view';
    case CALCULATOR_INSTALLMENT = 'calculator_installment';
    case TARIFF_MANAGEMENT = 'tariff_management';
    case BRANCH_MANAGEMENT = 'branch_management';
    case ROLE_MANAGEMENT = 'role_management';
    case SCORING_CREDIT_HISTORY = 'scoring_credit_history';
    case SCORING_INPS_HISTORY = 'scoring_inps_history';
    case SCORING_PENSION_INFO = 'scoring_personal_info';
    case MULTI_BRANCH = 'multi_branch';
    case OWN_SMS_PROVIDER = 'own_sms_provider';
    case INSTALLMENT = 'installment';
    case INSTALLMENT_SMS = 'installment_sms';
    case INSTALLMENT_AUTO_CHARGE = 'installment_auto_charge';
    case INSTALLMENT_AUTO_CHARGE_BY_PINFL = 'installment_auto_charge_by_pinfl';
}
