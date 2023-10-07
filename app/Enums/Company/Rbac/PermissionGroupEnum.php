<?php

namespace App\Enums\Company\Rbac;

enum PermissionGroupEnum: string
{
    case MANAGE_USERS = 'manage_users';
    case MANAGE_CUSTOMERS = 'manage_customers';
}
