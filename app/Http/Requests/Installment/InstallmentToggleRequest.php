<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentToggleRequest',
    title: 'Request to update an installment',
    properties: [
        new OA\Property(property: 'auto_charge', type: 'bool', example: 'true'),
    ],
)]
/**
 * @property bool $auto_charge
 */
class InstallmentToggleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'auto_charge' => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_EDIT);
    }
}
