<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum AttachmentTypeEnum: int
{
    use EnumMethods;

    case AVATAR = 1;
    case INSTALLMENT_DOCUMENT = 2;
    case LOYALTY_PROGRAM_IMAGE = 3;

    public function getStoragePath(): string
    {
        return match ($this) {
            self::AVATAR => 'avatars',
            self::INSTALLMENT_DOCUMENT => 'installment_documents',
            self::LOYALTY_PROGRAM_IMAGE => 'loyalty_program_images',
        };
    }
}
