<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property bool $is_active
 * @property int $tariff_id
 * @property int $user_id
 * @property int $remaining_limit_amount
 * @property Carbon $expired_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read bool $is_available
 * @property Tariff $tariff
 * @property User $user
 *
 * @method self whereIsAvailable()
 */
class Subscription extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    public static array $sortables = ['tariff_id', 'is_active'];

    public $table = 'loyalty_program_subscriptions';

    protected $fillable = [
        'is_active',
        'remaining_limit_amount',
        'expired_at',
        'tariff_id',
        'user_id',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && $this->tariff->is_active && $this->expired_at->isFuture();
    }

    public function scopeWhereIsAvailable($query)
    {
        return $query
            ->where('is_active', true)
            ->whereHas('tariff', fn ($q) => $q->where('is_active', true))
            ->where('expired_at', '>', Carbon::now());
    }

    public function extendSubscription(): void
    {
        $this->is_active = true;
        $this->remaining_limit_amount = $this->tariff->limit_amount;
        $this->expired_at = Carbon::now()->addDays($this->tariff->subscription_days);
        $this->saveOrFail();
    }
}
