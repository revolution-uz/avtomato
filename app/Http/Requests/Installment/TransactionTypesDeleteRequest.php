<?php

namespace App\Http\Requests\Installment;

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
            'ids.*' => 'integer|exists:transaction_types,id',
        ];
    }
}
