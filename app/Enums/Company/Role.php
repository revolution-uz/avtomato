<?php

namespace App\Enums\Company;

enum Role: string
{
    case OWNER = 'owner';
    case SALES_MANAGER = 'sales_manager';
}
