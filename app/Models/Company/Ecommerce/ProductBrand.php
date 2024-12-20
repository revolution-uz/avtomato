<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_oz
 * @property string $name_en
 * @property string $slug
 * @property bool $is_active
 * @property int|null $image_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $products
 * @property-read Banner $banners
 * @property-read EcommerceImage|null $image
 */
class ProductBrand extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    public static array $sortables = ['id', 'name_uz', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'is_active', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_brands';

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
        'slug',
        'image_id',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'brand_id');
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(EcommerceImage::class, 'image_id');
    }
}
