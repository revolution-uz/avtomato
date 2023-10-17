<?php

namespace App\Enums\Company;

enum CardStatusEnum: int
{
    public const BLOCKED = -1;
    public const INACTIVE = 0;
    public const ACTIVE = 1;
}
