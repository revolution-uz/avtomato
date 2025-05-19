<?php

namespace App\Http\Requests\LoyaltyProgram;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read array<array{
 *     item_code: string,
 *     price: float,
 *     qty: int,
 * }> $items
 * @property-read string $qr
 * @property-read int $shop_id
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
            'qr' => 'required|string',
            'shop_id' => 'required|integer|exists:loyalty_program_shops,external_code',
        ];
    }
}
