<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionRequest',
    title: 'Request to create or update a Transaction',
    properties: [
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'transaction_type_id', type: 'int', example: '100'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'paid_at', type: 'string', format: 'date', example: '2021-09-15'),
    ],
)]
/**
 * use @mixin Transaction
 */
class TransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'installment_id' => [
                'required',
                'int',
                Rule::exists('installments', 'id'),
            ],
            'customer_id' => [
                'required',
                'int',
                Rule::exists('customers', 'id'),
            ],
            'transaction_type_id' => [
                'required',
                'int',
                Rule::exists('transaction_types', 'id'),

            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
            'paid_at' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
            ],
        ];
    }

    public function authorize(): bool
    {
        return $this->id ?
            RbacHelper::checkPermission(PermissionEnum::TRANSACTION_EDIT) :
            RbacHelper::checkPermission(PermissionEnum::TRANSACTION_CREATE);
    }
}
