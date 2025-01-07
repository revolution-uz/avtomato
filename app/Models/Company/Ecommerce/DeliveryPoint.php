<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\OrderBySortable;
use App\Models\Company\Region;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_en
 * @property string $name_ru
 * @property string $name_oz
 * @property string $description_uz
 * @property string $description_ru
 * @property string $description_en
 * @property string $description_oz
 * @property string $is_prepaid
 * @property string|null $phone
 * @property string|null $address_uz
 * @property string|null $address_ru
 * @property string|null $address_en
 * @property string|null $address_oz
 * @property string|null $landmark_uz
 * @property string|null $landmark_ru
 * @property string|null $landmark_en
 * @property string|null $landmark_oz
 * @property string|null $location_url
 * @property float|null $lat
 * @property float|null $lng
 * @property int|null $region_id
 * @property int|null $district_id
 * @property int|null $mahalla_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Region|null $region
 * @property District|null $district
 * @property Mahalla|null $mahalla
 */
class DeliveryPoint extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'name_uz', 'name_ru', 'name_en', 'name_oz', 'description_uz', 'description_ru', 'description_en', 'description_oz', 'is_prepaid'];

    protected $table = 'ecommerce_delivery_points';

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'description_uz',
        'description_ru',
        'description_en',
        'description_oz',
        'is_prepaid',
        'phone',
        'address_uz',
        'address_ru',
        'address_en',
        'address_oz',
        'landmark_uz',
        'landmark_ru',
        'landmark_en',
        'landmark_oz',
        'location_url',
        'lat',
        'lng',
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
