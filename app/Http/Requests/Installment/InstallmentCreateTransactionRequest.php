<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCreateTransactionRequest',
    title: 'Request to create or update installment comment',
    properties: [
        new OA\Property(property: 'transaction_type_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
    ],
)]
/**
 * use @mixin InstallmentGuarantor
 */
class InstallmentCreateTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transaction_type_id' => [
                'required',
                Rule::exists('transaction_types', 'id'),
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_CREATE_TRANSACTION);
    }
}
