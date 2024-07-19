<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum SmsMessageStatusEnum: int
{
    use EnumMethods;

    case DELIVERED = 0; // доставлено
    case UNDELIVERED = 1; // не доставлено
    case EXPIRED = 2; // срок действия истек
    case ENROUTE = 3; // в обработке
    case ACCEPTED = 4; // принят СМС сервером, ожидается отчет о доставке
    case UNKNOWN = 5; // системная ошибка
}
