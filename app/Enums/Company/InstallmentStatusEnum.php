<?php

namespace App\Enums\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\EnumMethods;

enum InstallmentStatusEnum: int
{
    use EnumMethods;

    case CANCELED = -2; // means disabled
    case FROZEN = -1; // means freeze don't allowed to withdraw payment
    case DRAFT = 0; // means just created and need to run
    case RUNNING = 1; // means active
    case FINISHED = 2; // means closed

    /**
     * @return self[]
     */
    public static function getStatusListByStatus(self $statusEnum = null): array
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

    public static function getPermissions(self|int|null $status): ?PermissionEnum
    {
        if (! $status instanceof self) {
            $status = self::tryFrom($status);
        }

        return match ($status) {
            self::RUNNING => PermissionEnum::INSTALLMENT_START,
            self::CANCELED => PermissionEnum::INSTALLMENT_CANCEL,
            self::FROZEN => PermissionEnum::INSTALLMENT_FREEZE,
            self::FINISHED => PermissionEnum::INSTALLMENT_FINISH,
            default => null,
        };
    }
}
