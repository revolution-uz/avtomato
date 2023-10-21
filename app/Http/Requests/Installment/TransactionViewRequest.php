<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;

class TransactionViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW);
    }
}
