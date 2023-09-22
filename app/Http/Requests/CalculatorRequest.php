<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema()
 *
 * @property int $tariff_id
 * @property float $amount
 * @property float $initial_amount
 * @property int $period
 */
class CalculatorRequest extends FormRequest
{

    /**
     * @OA\Property(property="phone", type="string", example="998901112233")
     *
     * @return array<string, mixed>
     */
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
