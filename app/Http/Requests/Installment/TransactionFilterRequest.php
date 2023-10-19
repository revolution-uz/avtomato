<?php

namespace App\Http\Requests\Installment;

use App\Http\Requests\BaseFilterRequest;

class TransactionFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
