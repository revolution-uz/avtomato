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
 * @property int $product_id
 * @property int $order_id
 * @property float $price
 * @property float $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $product
 */
class OrderItem extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;
    protected static array $sortables = ['id', 'order_id', 'product_id', 'quantity', 'price', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_order_items';
    protected $fillable = [
        'product_id',
        'order_id',
        'price',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
