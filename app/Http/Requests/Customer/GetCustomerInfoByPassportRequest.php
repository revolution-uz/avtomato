<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GetCustomerInfoByPassportRequest',
    title: 'Request to get customer info by passport data',
    properties: [
        new OA\Property(property: 'passport_series', type: 'string', example: 'AA'),
        new OA\Property(property: 'passport_number', type: 'string', example: '1234567'),
        new OA\Property(property: 'birth_date', type: 'date', example: '23-12-1999'),
    ],
)]
/**
 * @property string $passport_series
 * @property string $passport_number
 * @property string $birth_date
 */
class GetCustomerInfoByPassportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'passport_series' => 'required|string|max:2|min:2',
            'passport_number' => 'required|string|max:7|min:7|regex:/^[0-9]+$/',
            'birth_date' => 'required|date',
        ];
    }
}
