<?php

namespace App\Models\Company;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CardTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $pinfl
 * @property string $uuid
 * @property bool|null $auto
 * @property bool|null $is_verified
 * @property bool|null $is_blocked
 * @property string|null $block_reason
 * @property Carbon|null $blocked_at
 * @property string|null $owner
 * @property string|null $pan
 * @property string|null $phone
 * @property CardTypeEnum|null $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read CardStatusEnum $status
 * @property Customer $customer
 */
class UniredCard extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'pinfl'];

    protected $fillable = [
        'customer_id',
        'pinfl',
        'uuid',
        'auto',
        'is_verified',
        'is_blocked',
        'block_reason',
        'blocked_at',
        'owner',
        'pan',
        'phone',
        'type',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'type' => CardTypeEnum::class,
    ];

    public function getStatusAttribute(): CardStatusEnum
    {
        if ($this->is_verified && ! $this->is_blocked) {
            return CardStatusEnum::ACTIVE;
        } elseif (! $this->is_verified && ! $this->is_blocked) {
            return CardStatusEnum::INACTIVE;
        } else {
            return CardStatusEnum::BLOCKED;
        }
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
