<?php

namespace App\Http\Requests\LoyaltyProgram;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read int $order_id
 */
class CompleteOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', 'exists:loyalty_program_orders,id,deleted_at,NULL'],
        ];
    }
}
