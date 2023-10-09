<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseFilterRequest;

class CustomersFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
