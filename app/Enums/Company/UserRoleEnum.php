<?php

namespace App\Enums\Company;

enum UserRoleEnum: string
{
    case OWNER = 'owner';
    case SALES_MANAGER = 'sales_manager';
}
