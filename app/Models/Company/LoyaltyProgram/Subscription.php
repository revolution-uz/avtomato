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
 * @property bool $is_active_period
 * @property int $card_id
 * @property int $tariff_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Card $card
 * @property Tariff $tariff
 * @property User $user
 */
class Subscription extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    public static array $sortables = ['card_id', 'tariff_id', 'is_active'];

    public $table = 'loyalty_program_subscriptions';

    protected $fillable = [
        'is_active',
        'card_id',
        'tariff_id',
        'user_id',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsActivePeriodAttribute(): bool
    {
        return $this->is_active && $this->tariff->is_active && $this->created_at->addMonths($this->tariff->subscription_months) <= Carbon::now();
    }
}
