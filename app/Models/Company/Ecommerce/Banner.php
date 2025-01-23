<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property-read string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property bool $is_active
 * @property string|null $url
 * @property int|null $order
 * @property int|null $category_id
 * @property int|null $brand_id
 * @property int|null $product_id
 * @property int $image_uz_id
 * @property int $image_ru_id
 * @property int $image_en_id
 * @property int $image_oz_id
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ProductCategory|null $category
 * @property-read ProductBrand|null $brand
 * @property-read Product|null $product
 * @property-read EcommerceImage $imageUz
 * @property-read EcommerceImage $imageRu
 * @property-read EcommerceImage $imageEn
 * @property-read EcommerceImage $imageOz
 */
class Banner extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'url',
        'is_active',
        'category_id',
        'brand_id',
        'product_id',
        'created_at',
        'updated_at',
    ];

    protected $table = 'ecommerce_banners';

    protected $fillable = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
        'url',
        'order',
        'starts_at',
        'ends_at',
        'category_id',
        'brand_id',
        'product_id',
        'image_uz_id',
        'image_ru_id',
        'image_oz_id',
        'image_en_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
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

    public function imageUz(): belongsTo
    {
        return $this->belongsTo(EcommerceImage::class);
    }

    public function imageRu(): belongsTo
    {
        return $this->belongsTo(EcommerceImage::class);
    }

    public function imageEn(): belongsTo
    {
        return $this->belongsTo(EcommerceImage::class);
    }

    public function imageOz(): belongsTo
    {
        return $this->belongsTo(EcommerceImage::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(get: fn () => $this->{'name_' . app()->getLocale()});
    }
}
