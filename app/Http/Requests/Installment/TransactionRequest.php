<?php

namespace App\Http\Requests\Installment;

use App\Models\Company\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionRequest',
    title: 'Request to create or update a Transaction',
    properties: [
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'transaction_type_id', type: 'int', example: '100'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'on_created', type: 'int', example: '16933424'),
    ],
)]
/**
 * use @mixin Transaction
 */
class TransactionRequest extends FormRequest
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
