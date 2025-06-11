<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Models\Company\OrderBySortable;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Order
 *
 * @property int $id
 * @property int $user_id
 * @property OrderStatusEnum $status
 * @property int|null $subscription_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read float $total_amount
 * @property-read float $total_discount_amount
 * @property-read float $total_paid_amount
 * @property User $user
 * @property Collection|OrderItem[] $items
 * @property Collection|OrderStatusHistory[] $statusHistories
 * @property Collection|OrderPayment[] $payments
 * @property Subscription|null $subscription
 */
class Order extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'user_id', 'status'];

    protected $table = 'loyalty_program_orders';

    protected $fillable = [
        'user_id',
        'status',
        'subscription_id',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function getTotalDiscountAmountAttribute(): float
    {
        return $this->items->sum('discount_amount');
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return $this->payments->sum('amount');
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum('total_amount');
    }

    public function applyOrderDiscount(Subscription $subscription): void
    {
        if ($subscription->remaining_limit_amount < $this->total_discount_amount) {
            throw new Exception('Not enough limit on subscription');
        }

        $this->subscription()->associate($subscription);
        $this->saveOrFail();
    }

    public function confirmOrderDiscount()
    {
        $totalDiscount = $this->total_discount_amount;

        if (! $this->subscription || $this->subscription->remaining_limit_amount < $totalDiscount) {
            throw new Exception('Not enough limit on subscription');
        }

        $this->subscription->decrement('remaining_limit_amount', (int) $totalDiscount);
    }

    public function revertOrderDiscount()
    {
        $totalDiscount = $this->total_discount_amount;

        if (! $this->subscription) {
            return;
        }

        $this->subscription->increment('remaining_limit_amount', (int) $totalDiscount);
    }
}
