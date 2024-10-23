<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_oz
 * @property string $name_en
 * @property int $status
 * @property int $category_id
 * @property int $brand_id
 * @property string $slug
 * @property string $image
 * @property float $cost
 * @property float $actual_price
 * @property float|null $old_price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read ProductCategory $category
 * @property-read ProductBrand $brand
 */
class Product extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;
    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'status',
        'cost',
        'actual_price',
        'old_price',
        'category_id',
        'brand_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class);
    }
}
