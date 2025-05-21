<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Models\Company\OrderBySortable;
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
 */
class Order extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = [
        'id',
        'user_id',
        'status',
    ];

    protected $table = 'loyalty_program_orders';

    protected $fillable = [
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $model) {
            $subscription = $model->user->getAvailableSubscription();

            if ($subscription) {
                $subscription->remaining_limit_amount -= (int) $model->total_discount_amount;
                $subscription->saveOrFail();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
