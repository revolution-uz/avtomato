<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum InstallmentAttachmentTypeEnum: int
{
    use EnumMethods;

    case DOCUMENT = 0;
    case IMAGE = 1;
}
