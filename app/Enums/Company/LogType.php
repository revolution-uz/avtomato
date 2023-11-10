<?php

namespace App\Enums\Company;

enum LogType: string
{
    case CREATE = 'created';
    case UPDATE = 'updated';
    case DELETE = 'deleted';

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATE => __('Created'),
            self::UPDATE => __('Updated'),
            self::DELETE => __('Deleted'),
        };
    }
}
