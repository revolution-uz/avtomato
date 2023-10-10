<?php

namespace App\Http\Requests\Installment;

use App\Models\Company\InstallmentCard;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCardRequest',
    title: 'Request to create or update a card',
    properties: [
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
        new OA\Property(property: 'auto_charge', type: 'boolean', example: 'true'),
    ],
)]
/**
 * use @mixin InstallmentCard
 */
class InstallmentCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => 'required|exists:cards,id',
            'auto_charge' => 'required|boolean',
        ];
    }
}
