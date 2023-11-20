<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum AttachmentTypeEnum: int
{
    use EnumMethods;

    case AVATAR = 1;

    public function getStoragePath(): string
    {
        return match ($this) {
            self::AVATAR => 'avatars',
        };
    }
}
