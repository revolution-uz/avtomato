<?php

namespace App\Enums\Company;

enum CompanyStatus: int
{
    case CREATED = 0; // means just created and need to run company:create command
    case ACTIVE = 1; // means active
    case  INACTIVE = -1; // means disabled
}
