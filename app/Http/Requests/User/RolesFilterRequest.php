<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFilterRequest;

class RolesFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
