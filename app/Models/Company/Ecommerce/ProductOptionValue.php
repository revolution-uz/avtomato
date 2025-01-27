<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\OrderBySortable;
use App\Traits\HasTranslations;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property-read string $value
 * @property string $value_uz
 * @property string $value_ru
 * @property string $value_en
 * @property string $value_oz
 * @property string $slug
 * @property int $group_id
 * @property int $option_id
 * @property int|null $image_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property ProductOption $option
 * @property EcommerceImage $image
 * @property ProductJoinOption[]|Collection<int,ProductJoinOption> $productJoinOptions
 */
class ProductOptionValue extends Model
{
    use HasFactory, HasTranslations, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'value_uz', 'value_ru', 'value_en', 'value_oz', 'slug', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_product_option_values';

    protected $fillable = [
        'value_uz',
        'value_ru',
        'value_en',
        'value_oz',
        'slug',
        'option_id',
        'image_id',
    ];

    protected $translatedColumns = ['value'];

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(EcommerceImage::class, 'image_id');
    }

    public function productJoinOptions(): HasMany
    {
        return $this->hasMany(ProductJoinOption::class, 'value_id');
    }

    public function value(): Attribute
    {
        return Attribute::make(get: fn () => $this->{'value_' . app()->getLocale()});
    }
}
