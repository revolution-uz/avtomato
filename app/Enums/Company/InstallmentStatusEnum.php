<?php

namespace App\Enums\Company;

enum InstallmentStatusEnum: int
{
    public const CANCELED = -2; // means disabled
    public const FROZEN = -1; // means freeze don't allowed to withdraw payment
    public const CREATED = 0; // means just created and need to run
    public const RUNNING = 1; // means active
    public const FINISHED = 2; // means closed
}
