<?php

namespace App\Enums\Company;

enum AttachmentTypeEnum: int
{
    case AVATAR = 1;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
