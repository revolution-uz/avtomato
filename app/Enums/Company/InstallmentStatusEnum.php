<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum InstallmentStatusEnum: int
{
    use EnumMethods;

    case CANCELED = -2; // means disabled
    case FROZEN = -1; // means freeze don't allowed to withdraw payment
    case CREATED = 0; // means just created and need to run
    case RUNNING = 1; // means active
    case FINISHED = 2; // means closed
}
