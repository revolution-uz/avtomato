<?php

namespace App\Http\Requests\Installment;

use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentGuarantorRequest',
    title: 'Request to create or update a installment guarantor',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'auto_charge', type: 'boolean', example: 'true'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
        new OA\Property(property: 'status', type: 'int', example: '1'),
    ],
)]
/**
 * use @mixin InstallmentGuarantor
 */
class InstallmentGuarantorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'auto_charge' => 'required|boolean',
            'any_card_charge' => 'required|boolean',
            'status' => 'required|int',
        ];
    }
}
