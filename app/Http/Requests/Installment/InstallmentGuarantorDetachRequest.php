<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentGuarantorDetachRequest',
    title: 'Request to attach or detach installment guarantor',
)]
/**
 * use @mixin InstallmentGuarantor
 */
class InstallmentGuarantorDetachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_DETACH_GUARANTOR);
    }
}
