<?php

namespace App\Http\Requests;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read int $installment_id
 * @property-read int $guarantor_id
 */
class DownloadGuarantorApplicationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'installment_id' => [
                'required',
                'int',
                Rule::exists('installments', 'id')->whereNull('deleted_at'),
            ],
            'guarantor_id' => [
                'required',
                'int',
                Rule::exists('customers', 'id')->whereNull('deleted_at'),
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_VIEW_DOCUMENT);
    }

    public function withValidator($validator)
    {
        $isInstallmentRelatedToGuarantor = InstallmentGuarantor::query()
            ->where('installment_id', $this->installment_id)
            ->where('customer_id', $this->guarantor_id)
            ->whereNull('deleted_at')
            ->exists();

        $validator->after(function ($validator) use ($isInstallmentRelatedToGuarantor) {
            if (! $isInstallmentRelatedToGuarantor) {
                $validator->errors()->add('relation', 'The selected installment and guarantor are not related.');
            }
        });
    }
}
