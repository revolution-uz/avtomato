<?php

namespace App\Http\Requests;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $customer_id
 */
class DownloadCustomerApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => [
                'required', 'int', Rule::exists('customers', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPLOAD_DOCUMENT);
    }
}
