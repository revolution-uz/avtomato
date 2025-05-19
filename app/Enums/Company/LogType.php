<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum LogType: string
{
    use EnumMethods;

    case CREATE = 'created';
    case UPDATE = 'updated';
    case DELETE = 'deleted';
    case RESTORE = 'restored';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATE => __('Created'),
            self::UPDATE => __('Updated'),
            self::DELETE => __('Deleted'),
            self::RESTORE => __('Restored'),
        };
    }
}
