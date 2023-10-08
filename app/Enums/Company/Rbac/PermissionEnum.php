<?php

namespace App\Enums\Company\Rbac;

/**
 * To add new permission:
 * 1. Add new permission as a case to PermissionEnum
 * 2. Add new permission to list() method and set group
 * 3. Add new permission to getIsBranchRelated() method and set branch related or not
 */
enum PermissionEnum: string
{
    // Manage Users
    case VIEW_USER = 'view_user';
    case EDIT_USER = 'edit_user';
    case DELETE_USER = 'delete_user';

    // Manage Customers
    case VIEW_CUSTOMER = 'view_customer';
    case CREATE_CUSTOMER = 'create_customer';

    public static function getIsBranchRelated($permission): bool
    {
        return match ($permission) {
            // not branch related
            self::VIEW_USER->value,
            self::EDIT_USER->value,
            self::DELETE_USER->value => false,

            // branch related
            self::VIEW_CUSTOMER->value,
            self::CREATE_CUSTOMER => true,

            default => false
        };
    }

    public static function list()
    {
        return [
            PermissionGroupEnum::MANAGE_USERS->value => [
                self::VIEW_USER->value,
                self::EDIT_USER->value,
                self::DELETE_USER->value,
            ],
            PermissionGroupEnum::MANAGE_CUSTOMERS->value => [
                self::VIEW_CUSTOMER->value,
                self::CREATE_CUSTOMER->value,
            ],
        ];
    }
}
