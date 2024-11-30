<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $product_id
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $product
 * @property-read ProductOption[]|Collection<int,ProductOption> $options
 * @property Image[]|Collection<int, Image> $images
 */
class ProductVariation extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'product_id', 'is_active', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_variations';
    protected $fillable = [
        'product_id',
        'is_active',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(ProductOption::class, 'ecommerce_product_join_options');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imagable');
    }
}
