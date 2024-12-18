<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property string $slug
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property ProductJoinOption[]|Collection<int,ProductJoinOption> $productJoinOptions
 * @property ProductOptionValue[]|Collection<int,ProductOptionValue> $values
 * @property ProductCategory[]|Collection<int,ProductCategory> $categories
 */
class ProductOption extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'slug', 'description', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_options';

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'description',
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

    public function values(): HasMany
    {
        return $this->hasMany(ProductOptionValue::class, 'option_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'ecommerce_product_categories_join_options', 'option_id', 'category_id');
    }

    public function productJoinOptions(): HasMany
    {
        return $this->hasMany(ProductJoinOption::class, 'option_id');
    }
}
