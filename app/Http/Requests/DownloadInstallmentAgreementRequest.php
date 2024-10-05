<?php

namespace App\Http\Requests;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $installment_id
 */
class DownloadInstallmentAgreementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'installment_id' => [
                'required', 'int', Rule::exists('installments', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW_DOCUMENT);
    }
}
