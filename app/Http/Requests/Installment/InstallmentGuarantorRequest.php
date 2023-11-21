<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentGuarantorRequest',
    title: 'Request to attach or detach installment guarantor',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
    ],
)]
/**
 * use @mixin InstallmentGuarantor
 */
class InstallmentGuarantorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_ATTACH_GUARANTOR);
    }
}
