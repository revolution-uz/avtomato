<?php

namespace App\Http\Requests\LoyaltyProgram;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read array<array{
 *     item_code: string,
 *     price: float,
 *     qty: int,
 *     discount: float
 * }> $items
 * @property-read string $qr
 */
class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.item_code' => 'required|string|exists:loyalty_program_items,external_code',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.discount' => 'required|numeric',
            'qr' => 'required|string',
        ];
    }
}
