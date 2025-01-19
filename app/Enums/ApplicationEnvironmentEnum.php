<?php

namespace App\Enums;

enum ApplicationEnvironmentEnum: string
{
    case LOCAL = 'local';
    case PRODUCTION = 'production';
    case DEV = 'development';

    public static function getCurrent(): self
    {
        return self::tryFrom(config('app.env')) ?? self::DEV;
    }

    public static function isProduction(): bool
    {
        return self::getCurrent() === self::PRODUCTION;
    }
}
