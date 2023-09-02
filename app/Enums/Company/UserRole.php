<?php

namespace App\Enums\Company;

enum UserRole: string
{
    case OWNER = 'owner';
    case SALES_MANAGER = 'sales_manager';
}
