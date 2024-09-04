<?php

namespace App\Enums\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\EnumMethods;

enum InstallmentStatusEnum: int
{
    use EnumMethods;

    case CANCELED = -2; // means disabled
    case FROZEN = -1; // means freeze don't allowed to withdraw payment
    case INACTIVE = 0; // means just created and need to run
    case ACTIVE = 1; // means active
    case CLOSED = 2; // means closed

    /**
     * @return self[]
     */
    public static function getStatusListByStatus(?self $statusEnum = null): array
    {
        return match ($statusEnum) {
            self::CANCELED,
            self::ACTIVE => [self::FROZEN, self::INACTIVE],
            self::FROZEN => [self::ACTIVE, self::CLOSED],
            self::INACTIVE => [],
            self::CLOSED => [self::ACTIVE],
            default => [self::CANCELED, self::INACTIVE],
        };
    }

    public static function getPermissions(self|int|null $status): ?PermissionEnum
    {
        if (! $status instanceof self) {
            $status = self::tryFrom($status);
        }

        return match ($status) {
            self::ACTIVE => PermissionEnum::INSTALLMENT_START,
            self::CANCELED => PermissionEnum::INSTALLMENT_CANCEL,
            self::FROZEN => PermissionEnum::INSTALLMENT_FREEZE,
            self::CLOSED => PermissionEnum::INSTALLMENT_FINISH,
            default => null,
        };
    }
}
