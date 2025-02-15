<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum OrderPaymentMethodEnum: int
{
    use EnumMethods;

    case CARD = 0;
    case CARD_AFTER_DELIVERY = 1; // not accepted if delivery point is not a branch
    case CASH_AFTER_DELIVERY = 2; // not accepted if delivery point is not a branch
    case PAY_BY_INSTALLMENT = 3;

    public static function payAfterDeliveryMethods()
    {
        return [OrderPaymentMethodEnum::CARD_AFTER_DELIVERY->value, OrderPaymentMethodEnum::CASH_AFTER_DELIVERY->value];
    }
}
