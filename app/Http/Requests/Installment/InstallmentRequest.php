<?php

namespace App\Http\Requests\Installment;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentRequest',
    title: 'Request to create or update a installment',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'branch_id', type: 'int', example: '1'),
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'description', type: 'string', example: 'inside the order'),
        new OA\Property(property: 'sms_notify', type: 'boolean', example: 'false'),
        new OA\Property(property: 'auto_charge', type: 'boolean', example: 'true'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
        new OA\Property(property: 'starts_in', type: 'int', example: '16933424'),
    ],
)]
/**
 * @property string $name
 * @property boolean $is_annual
 * @property double $percent
 * @property int $min_amount
 * @property int $max_amount
 * @property int $min_initial_fee
 * @property int $max_initial_fee
 */
class InstallmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|int',
            'branch_id' => 'required|int',
            'tariff_id' => 'required|int',
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'sms_notify' => 'required|boolean',
            'auto_charge' => 'required|boolean',
            'any_card_charge' => 'required|boolean',
            'starts_in' => 'required|boolean',
        ];
    }
}
