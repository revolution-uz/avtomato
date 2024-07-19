<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SmsTemplateHelper
{
    const TEMPLATE_AUTO_CHARGE = "{pan} kartadan {amount} so'm N{credit_number} shartnomaga asosan {company} ga yechildi. Ma'lumot uchun: {phone}";
    const TEMPLATE_CARD_CONFIRMATION = 'Avtomato tizimida operatsiyani tasdiqlash uchun kod: {code}';

    public static function getAutoChargeTemplate(string $pan, string $amount, string $credit_number, string $company, string $phone): string
    {
        return str_replace(
            ['{pan}', '{amount}', '{credit_number}', '{company}', '{phone}'],
            [$pan, $amount, $credit_number, Str::substr($company, 0, 40), $phone],
            self::TEMPLATE_AUTO_CHARGE
        );
    }

    public static function getCardConfirmationTemplate(string $code)
    {
        return str_replace('{code}', $code, self::TEMPLATE_CARD_CONFIRMATION);
    }
}
