<?php

namespace App\Models\Company\Ecommerce;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $product_id
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $product
 * @property-read ProductJoinOption[]|Collection<int,ProductJoinOption> $productJoinOption
 */
class ProductVariation extends Model
{
    use HasFactory;
    protected $table = 'e_commerce_product_variations';
    protected $fillable = [
        'product_id',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productJoinOptions(): HasMany
    {
        return $this->hasMany(ProductJoinOption::class);
    }
}
