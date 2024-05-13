<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;

class CalculatorExportRequest extends CalculatorRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CALCULATOR_EXPORT);
    }
}
