<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $category_id
 * @property int|null $brand_id
 * @property string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_oz
 * @property string $name_en
 * @property string $slug
 * @property float $cost
 * @property float $actual_price
 * @property float|null $old_price
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ProductCategory $category
 * @property-read ProductBrand $brand
 * @property-read ProductVariation[] $variations
 * @property EcommerceImage[]|Collection<int, EcommerceImage> $images
 * @property EcommerceImage|Collection<int, EcommerceImage> $mainImage
 */
class Product extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'category_id', 'brand_id', 'name_uz', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'slug', 'cost', 'actual_price', 'old_price', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_products';

    protected $fillable = [
        'category_id',
        'brand_id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'cost',
        'actual_price',
        'old_price',
        'is_active',
    ];

    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;

        $count = 2;
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $this->attributes['slug'] = $slug;
    }

    public function getNameAttribute(): string
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
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
