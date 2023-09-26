<?php

namespace App\Http\Requests\Installment;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TariffRequest',
    title: 'Request to create or update a tariff',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Tariff Name'),
        new OA\Property(property: 'is_annual', type: 'boolean', example: 'true'),
        new OA\Property(property: 'percent', type: 'double', example: '40'),
        new OA\Property(property: 'min_amount', type: 'int', example: '0'),
        new OA\Property(property: 'max_amount', type: 'int', example: '10000000'),
        new OA\Property(property: 'min_initial_fee', type: 'int', example: '0'),
        new OA\Property(property: 'max_initial_fee', type: 'int', example: '10000000'),
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
class TariffRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:40',
            'is_annual' => 'required|boolean',
            'percent' => 'required|numeric',
            'min_amount' => 'required|int',
            'max_amount' => 'required|int',
            'min_initial_fee' => 'required|int',
            'max_initial_fee' => 'required|int',
        ];
    }
}
