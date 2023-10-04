<?php

namespace App\Http\Requests\Rbac;

use App\Http\Requests\BaseFilterRequest;

class RolesFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
