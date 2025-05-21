<?php

namespace App\Enums\Company\LoyaltyProgram;

enum MobileUpdateRequirementEnum: int
{
    case NONE = 0;
    case OPTIONAL = 1;
    case REQUIRED = 2;
}
