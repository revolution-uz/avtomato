<?php

namespace App\Http\Requests\Installment;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CalculatorRequest',
    title: 'Request to sign in',
    properties: [
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'int', example: '300000'),
        new OA\Property(property: 'initial_amount', type: 'int', example: '100000'),
        new OA\Property(property: 'period', type: 'int', example: '12'),
    ],
)]
/**
 * @property int $tariff_id
 * @property float $amount
 * @property float $initial_amount
 * @property int $period
 */
class CalculatorRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tariff_id' => 'required|exists:tariffs,id',
            'amount' => 'required|numeric',
            'initial_amount' => 'required|numeric',
            'period' => 'required|int',
        ];
    }
}
