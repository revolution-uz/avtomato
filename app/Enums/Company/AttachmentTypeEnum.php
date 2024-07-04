<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum AttachmentTypeEnum: int
{
    use EnumMethods;

    case AVATAR = 1;
    case INSTALLMENT_DOCUMENT = 2;
    case INSTALLMENT_FORM_DOCUMENT = 3;
    case CUSTOMER_APPLICATION_DOCUMENT = 4;

    public function getStoragePath(): string
    {
        return match ($this) {
            self::AVATAR => 'avatars',
            self::INSTALLMENT_DOCUMENT => 'installment_documents',
            self::INSTALLMENT_FORM_DOCUMENT => 'installment_form_documents',
            self::CUSTOMER_APPLICATION_DOCUMENT => 'customer_application_documents',
        };
    }
}
