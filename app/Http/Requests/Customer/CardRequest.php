<?php

namespace App\Http\Requests\Customer;

use App\Models\Company\Card;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CardRequest',
    title: 'Request to create or update a card',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'number', type: 'string', example: '9860111122224444'),
        new OA\Property(property: 'expiry', type: 'string', example: '2809', description: 'year+month 2809 instead of 09/28'),
    ],
)]
/**
 * @mixin Card
 */
class CardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'number' => 'required|string',
            'expiry' => 'required|string',
        ];
    }
}
