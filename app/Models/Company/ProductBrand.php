<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_oz
 * @property string $name_en
 * @property int $status
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Product $products
 * @property-read Banner $banners
 */
class ProductBrand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'status',
        'image',
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
