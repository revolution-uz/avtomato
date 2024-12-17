<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $group_id
 * @property string $value_uz
 * @property string $value_ru
 * @property string $value_en
 * @property string $value_oz
 * @property string $slug
 * @property int $option_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property ProductOption $option
 * @property ProductJoinOption[]|Collection<int,ProductJoinOption> $productJoinOptions
 */
class ProductOptionValue extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'value_uz', 'value_ru', 'value_en', 'value_oz', 'slug', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_option_values';

    protected $fillable = [
        'value_uz',
        'value_ru',
        'value_en',
        'value_oz',
        'slug',
        'option_id',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }

    public function productJoinOptions(): HasMany
    {
        return $this->hasMany(ProductJoinOption::class, 'value_id');
    }
}
