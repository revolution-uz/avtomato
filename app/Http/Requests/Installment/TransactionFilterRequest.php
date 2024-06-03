<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Http\Requests\BaseFilterRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $admin_id
 * @property int $installment_id
 * @property int $customer_id
 * @property int $card_id
 * @property int $transaction_type_id
 * @property int $amount
 */
class TransactionFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW_ALL);
    }

    public function rules(): array
    {
        return [
            'sort' => 'string|regex:/^[-]?[a-z]+$/',
            'search' => 'string|max:255',
            'page' => 'int|min:1',
            'per_page' => 'int|min:1',
            'id' => 'int|min:1',
            'installment_id' => ['nullable', 'int', Rule::exists('installments', 'id')],
            'customer_id' => ['nullable', 'int', Rule::exists('customers', 'id')],
            'card_id' => ['nullable', 'int', Rule::exists('cards', 'id')],
            'transaction_type_id' => ['nullable', 'int', Rule::exists('transaction_types', 'id')],
            'amount' => 'int|min:0',
        ];
    }
}
