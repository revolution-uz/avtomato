<?php

namespace App\Enums;

enum TelegramChannelTypeEnum: int
{
    use EnumMethods;

    case ERROR = 0;
    case COMPANY_GROUP = 1;
}
