<?php

namespace App\Models\Company;

use App\Enums\Company\AddressTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property AddressTypeEnum $type
 * @property string|null $address
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
class Address extends Model
{
    use LogsActivityDirtyOnlyTrait;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'type',
        'address',
        'region_id',
        'district_id',
        'mahalla_id',
    ];

    protected $casts = [
        'type' => AddressTypeEnum::class,
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
