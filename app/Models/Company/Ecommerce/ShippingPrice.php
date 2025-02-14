<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property float $price
 * @property int|null $region_id
 * @property int|null $district_id
 * @property int|null $mahalla_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property ProductCategory|null $category
 * @property Region|null $region
 * @property District|null $district
 * @property Mahalla|null $mahalla
 */
class ShippingPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ecommerce_shipping_prices';

    protected $fillable = [
        'price',
        // 'provider', // Yandex, BTS...
        // 'category_id',
        'region_id',
        'district_id',
        'mahalla_id',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function mahalla(): BelongsTo
    {
        return $this->belongsTo(Mahalla::class);
    }
}
