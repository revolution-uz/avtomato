<?php

namespace App\Enums\Company;

enum InstallmentStatusEnum: int
{
    case CANCELED = -1; // means disabled
    case FROZEN = 0; // means freeze don't allowed to withdraw payment
    case CREATED = 1; // means just created and need to run
    case RUNNING = 2; // means active
    case FINISHED = 5; // means closed
}
