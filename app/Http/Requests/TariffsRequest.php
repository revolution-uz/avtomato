<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema()
 *
 * @property string $name
 * @property boolean $is_annual
 * @property double $percent
 * @property int $min_amount
 * @property int $max_amount
 * @property int $min_initial_fee
 * @property int $max_initial_fee
 */

class TariffsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @OA\Property(property="name", type="string", example="Tariff Name")
     * @OA\Property(property="is_annual", type="boolean", example="true")
     * @OA\Property(property="percent", type="double", example="40")
     * @OA\Property(property="min_amount", type="int", example="0")
     * @OA\Property(property="max_amount", type="int", example="10000000")
     * @OA\Property(property="min_initial_fee", type="int", example="0")
     * @OA\Property(property="max_initial_fee", type="int", example="10000000")
     *
     * @return array<string>
     */
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