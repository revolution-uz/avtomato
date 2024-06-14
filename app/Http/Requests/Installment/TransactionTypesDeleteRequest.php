<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionTypesDeleteRequest',
    title: 'Request to delete transaction_types',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class TransactionTypesDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value < 100) {
                        $fail(__('api.You cannot delete', [
                            'item' => TransactionType::query()->where('id', $value)->first()->name,
                        ]));
                    }
                    if (! TransactionType::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (Transaction::query()->where('transaction_type_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Transaction Type'),
                            'element' => __('api.Transaction'),
                        ]));
                    }
                },
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_TYPE_DELETE);
    }
}
