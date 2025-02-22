<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property float $price
 * @property float $quantity
 * @property int $order_id
 * @property int $product_id
 * @property int $variation_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Order $order
 * @property Product $product
 * @property ProductVariation $variation
 */
class OrderItem extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'order_id', 'product_id', 'quantity', 'price', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_order_items';

    protected $fillable = [
        'price',
        'quantity',
        'order_id',
        'product_id',
        'variation_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
