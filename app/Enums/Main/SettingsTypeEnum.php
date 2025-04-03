<?php

namespace App\Enums\Main;

enum SettingsTypeEnum: int
{
    case BOOLEAN = 1;
    case FLOAT = 2;
    case INT = 3;
    case STRING = 4;
    case TEXT = 5;
    case DATETIME = 6;
    case DATE = 7;
    case FILE = 8;

    public static function getAllTypesForDropdown(): array
    {
        return [
            self::STRING->value => 'string',
            self::TEXT->value => 'string',
            self::INT->value => 'integer',
            self::FLOAT->value => 'decimal',
            self::BOOLEAN->value => 'boolean',
            self::FILE->value => 'file',
        ];
    }
}
