<?php

namespace App\Http\Requests\Management;

use App\Http\Requests\BaseFilterRequest;

class BranchesFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
