<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_oz
 * @property string $name_en
 * @property int $status
 * @property string|null $url
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property int|null $product_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ProductCategory $category
 * @property-read ProductBrand $brand
 * @property-read Product $product
 */
class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'status',
        'url',
        'category_id',
        'brand_id',
        'product_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
