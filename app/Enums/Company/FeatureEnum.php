<?php

namespace App\Enums\Company;

enum FeatureEnum: string
{
    case MULTI_BRANCH = 'multi_branch';
    case OWN_SMS_PROVIDER = 'own_sms_provider';
    case INSTALLMENT = 'installment';
    case INSTALLMENT_SMS = 'installment_sms';
    case INSTALLMENT_AUTO_CHARGE = 'installment_auto_charge';
    case INSTALLMENT_AUTO_CHARGE_BY_PINFL = 'installment_auto_charge_by_pinfl';
}
