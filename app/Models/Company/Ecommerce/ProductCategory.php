<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property-read string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property string $slug
 * @property bool $is_active
 * @property int|null $parent_id
 * @property int|null $image_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property ProductCategory|null $parent
 * @property Product[]|Collection<int, Product> $products
 * @property Banner[]|Collection<int, Banner> $banners
 * @property-read EcommerceImage|null $image
 */
class ProductCategory extends Model
{
    use HasFactory, HasTranslations, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = [
        'id',
        'parent_id',
        'name_uz',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $table = 'ecommerce_product_categories';

    protected $fillable = [
        'parent_id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'is_active',
        'image_id',
    ];

    protected $translatedColumns = ['name'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'category_id', 'id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(EcommerceImage::class);
    }
}
