<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property string $image
 * @property int $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Product[]|Collection<int, Product> $products
 * @property Banner[]|Collection<int, Banner> $banners
 */
class ProductCategory extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;
    protected $fillable = [
        'parent_id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'image',
        'status',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }
}
