<?php

namespace App\Models;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CardTypeEnum;
use App\Models\Company\OrderBySortable;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

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
 * @property CentralCustomer $customer
 */
class CentralUniredCard extends Model
{
    use CentralConnection, HasApiTokens, HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable;

    public static array $sortables = ['id'];

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
        return $this->belongsTo(CentralCustomer::class, 'customer_id', 'id');
    }
}
