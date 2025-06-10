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
 * @property int|null $shopmate_id
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
 * @property User|null $shopmate
 */
class Order extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'user_id', 'status'];

    protected $table = 'loyalty_program_orders';

    protected $fillable = [
        'user_id',
        'status',
        'shopmate_id',
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

    public function shopmate(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function applyOrderDiscount()
    {
        $subscription = $this->shopmate_id
            ? $this->shopmate->getAvailableSubscription()
            : $this->user->getAvailableSubscription();

        $totalDiscount = $this->total_discount_amount;

        if (! $subscription || $subscription->remaining_limit_amount < $totalDiscount) {
            throw new Exception('Not enough limit on subscription');
        }

        $subscription->decrement('remaining_limit_amount', (int) $totalDiscount);
    }
}
