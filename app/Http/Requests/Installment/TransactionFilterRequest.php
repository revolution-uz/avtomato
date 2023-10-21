<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Http\Requests\BaseFilterRequest;

class TransactionFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW_ALL);
    }
}
