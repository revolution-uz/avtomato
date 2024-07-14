<?php

namespace App\Models\Company;

use App\Enums\Company\AddressTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property AddressTypeEnum $type
 * @property string|null $address
 * @property string|null $full_address
 * @property int|null $region_id
 * @property int|null $district_id
 * @property int|null $mahalla_id
 * @property string $addressable_type
 * @property int $addressable_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Region|null $region
 * @property District|null $district
 * @property Mahalla|null $mahalla
 */
class Address extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

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

    public function fullAddress(): Attribute
    {
        return Attribute::make(get: fn () => $this->region?->name . ', ' . $this->district?->name . ', ' . $this->mahalla?->name . ', ' . $this->address);
    }
}
