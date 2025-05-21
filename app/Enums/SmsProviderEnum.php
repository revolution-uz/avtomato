<?php

namespace App\Enums;

enum SmsProviderEnum: string
{
    use EnumMethods;

    case SMS_SAYQAL = 'sms_sayqal';
    case SMS_FLY = 'sms_fly';
}
