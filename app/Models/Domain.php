<?php

namespace App\Models;

use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    use CentralConnection;
}
