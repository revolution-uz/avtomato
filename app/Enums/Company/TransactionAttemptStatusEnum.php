<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum TransactionAttemptStatusEnum: int
{
    use EnumMethods;

    case FAILED = -1; // means just failed with some error
    case CREATED = 0; // means just created
    case SUCCESS = 1; // means finished with success
    case IN_PROGRESS = -2; // mean check the transaction later
}
