<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $product_id
 * @property int|null $order_id
 * @property float $price
 * @property float $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class ProductAction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_id',
        'order_id',
        'price',
        'quantity',
    ];
}
