<?php

namespace App\Enums\Company;

enum CardStatusEnum: int
{
    case BLOCKED = -1;
    case INACTIVE = 0;
    case ACTIVE = 1;
}
