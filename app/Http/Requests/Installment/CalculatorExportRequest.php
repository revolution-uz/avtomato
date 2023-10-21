<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;

class CalculatorExportRequest extends CalculatorRequest
{
    public function rules(): array
    {
        return parent::rules() + [
            'type' => 'required|in:xlsx,csv',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CALCULATOR_EXPORT);
    }
}
