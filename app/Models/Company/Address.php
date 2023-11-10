<?php

namespace App\Models\Company;

use App\Enums\Company\AddressTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string|null $address
 * @property int|null $region_id
 * @property int|null $district_id
 * @property int|null $mahalla_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property Region|null $region
 * @property District|null $district
 * @property Mahalla|null $mahalla
 */
class Address extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

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
