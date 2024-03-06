<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum InstallmentStatusEnum: int
{
    use EnumMethods;

    case CANCELED = -2; // means disabled
    case FROZEN = -1; // means freeze don't allowed to withdraw payment
    case DRAFT = 0; // means just created and need to run
    case RUNNING = 1; // means active
    case FINISHED = 2; // means closed

    public static function getStatusListByStatus(?self $statusEnum = null)
    {
        return match ($statusEnum) {
            self::CANCELED,
            self::RUNNING => [self::FROZEN, self::DRAFT],
            self::FROZEN => [self::RUNNING, self::FINISHED],
            self::DRAFT => [],
            self::FINISHED => [self::RUNNING],
            default => [self::CANCELED, self::DRAFT],
        };
    }
}
