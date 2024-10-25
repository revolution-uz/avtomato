<?php

namespace App\Models\Company\Ecommerce;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $option_id
 * @property int $value_id
 * @property int $product_id
 * @property int|null $product_variation_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property ProductOption $productOption
 * @property ProductOptionValue $productOptionValue
 * @property Product $product
 * @property ProductVariation $productVariation
 */
class ProductJoinOption extends Model
{
    use HasFactory;
    protected $table = 'e_commerce_product_join_options';
    protected $fillable = [
        'option_id',
        'value_id',
        'product_id',
        'product_variation_id',
    ];

    public function productOption(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function productOptionValue(): BelongsTo
    {
        return $this->belongsTo(ProductOptionValue::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }
}
