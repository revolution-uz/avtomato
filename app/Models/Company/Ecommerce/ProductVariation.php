<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $product_id
 * @property bool $is_active
 * @property float $cost
 * @property float $actual_price
 * @property float|null $old_price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $product
 * @property-read ProductOptionValue[]|Collection<int,ProductOptionValue> $optionValues
 * @property EcommerceImage[]|Collection<int, EcommerceImage> $images
 */
class ProductVariation extends Model
{
    use OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'product_id', 'is_active', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_variations';

    protected $fillable = [
        'is_active',
        'cost',
        'actual_price',
        'old_price',
        'product_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class, 'ecommerce_product_join_options', 'product_variation_id', 'value_id'
        );
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(EcommerceImage::class, 'imageable', 'ecommerce_imageables', relatedPivotKey: 'image_id')
            ->withPivot('order')
            ->orderBy('pivot_order');
    }

    public function syncImagesWithOrder(array $images): void
    {
        $syncData = [];

        foreach ($images as $image) {
            if (isset($image['image_id'], $image['order'])) {
                $syncData[$image['image_id']] = ['order' => $image['order']];
            }
        }

        $this->images()->sync($syncData);
    }
}
