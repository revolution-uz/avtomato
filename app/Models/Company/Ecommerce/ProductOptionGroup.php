<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

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
 * @property-read ProductOption[]|Collection<int,ProductOption> $productOptions
 */
class ProductOptionGroup extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;
    protected static array $sortables = ['id', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'slug', 'description', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_option_groups';
    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'description',
    ];

    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }
}
