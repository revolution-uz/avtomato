<?php

namespace App\Enums\Company;

enum CompanyStatus: string
{
    case NEW = 'new';
    case CREATED = 'created';
    case ACTIVE = 'active';
    case  INACTIVE = 'inactive';
}
