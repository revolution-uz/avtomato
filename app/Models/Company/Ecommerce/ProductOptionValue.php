<?php

namespace App\Models\Company\Ecommerce;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $group_id
 * @property string $value_uz
 * @property string $value_ru
 * @property string $value_en
 * @property string $value_oz
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class ProductOptionValue extends Model
{
    use HasFactory;
    protected $table = 'e_commerce_product_option_values';
    protected $fillable = [
        'value_uz',
        'value_ru',
        'value_en',
        'value_oz',
        'slug',
    ];

    public function productJoinOptions(): HasMany
    {
        return $this->hasMany(ProductJoinOption::class, 'value_id');
    }
}
