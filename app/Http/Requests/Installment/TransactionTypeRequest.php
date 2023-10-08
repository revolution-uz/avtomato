<?php

namespace App\Http\Requests\Installment;

use App\Models\Company\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionTypeRequest',
    title: 'Request to create or update a TransactionType',
    properties: [
        new OA\Property(property: 'name_uz', type: 'string', example: 'Naqd'),
        new OA\Property(property: 'name_ru', type: 'string', example: 'Naqd'),
        new OA\Property(property: 'name_en', type: 'string', example: 'Naqd'),
    ],
)]
/**
 * use @mixin TransactionType
 */
class TransactionTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name_uz' => 'required|string',
            'name_ru' => 'required|string',
            'name_en' => 'required|string',
        ];
    }
}
