<?php

namespace App\Models\Company\LoyaltyProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class OrderItem
 *
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price
 * @property float $discount_amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read float $total_amount
 * @property Order $order
 * @property Item $product
 */
class OrderItem extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'discount_amount',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->price * $this->quantity - $this->discount_amount;
    }
}
