<?php

namespace App\Http\Requests\Customer;

use App\Http\Requests\BaseFilterRequest;

class CardFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
