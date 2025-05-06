<?php

namespace App\Http\Resources;

use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_id' => $this->id,
            'client' => [
                'name' => $this->user->full_name,
                'phone' => $this->user->phone,
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'total_discount_amount' => $this->total_discount_amount,
            'total_amount' => $this->total_amount,
            'items' => $this->items->map(function (OrderItem $item) {
                return [
                    'id' => $item->id,
                    'name' => $item->product->external_name,
                    'item_code' => $item->product->external_code,
                    'price' => $item->price,
                    'qty' => $item->quantity,
                    'discount' => $item->discount_amount,
                    'final_price' => $item->final_price,
                    'total_price' => $item->final_price * $item->quantity,
                ];
            })->toArray(),
        ];
    }
}
