<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFilterRequest;

class UserFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
