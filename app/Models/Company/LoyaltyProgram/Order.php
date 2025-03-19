<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\DiscountTypeEnum;
use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Models\Company\CompanyAdmin;
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
 * @property int|null $admin_id
 * @property OrderStatusEnum $status
 * @property DiscountTypeEnum $discount_type
 * @property float $total_amount
 * @property float $total_discount_amount
 * @property float $total_paid_amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property User $user
 * @property CompanyAdmin|null $admin
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
        'admin_id',
        'status',
        'discount_type',
    ];

    protected $table = 'loyalty_program_orders';

    protected $fillable = [
        'user_id',
        'admin_id',
        'status',
        'discount_type',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'discount_type' => DiscountTypeEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class);
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
