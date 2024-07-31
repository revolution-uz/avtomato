<?php

namespace App\Enums\Company;

enum CompanyStatusEnum: int
{
    case CREATED = 0; // means just created and need to run company:create command
    case ACTIVE = 1; // means active
    case INACTIVE = -1; // means disabled

    public static function getAllCasesForDropdown(): array
    {
        return [
            self::CREATED->value => 'Created',
            self::ACTIVE->value => 'Active',
            self::INACTIVE->value => 'Inactive',
        ];
    }
}
