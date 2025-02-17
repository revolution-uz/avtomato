<?php

namespace App\Models\Company\Ecommerce;

use App\Enums\Company\OrderDeliveryTypeEnum;
use App\Enums\Company\OrderPaymentMethodEnum;
use App\Models\Company\Address;
use App\Models\Company\Installment;
use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property OrderDeliveryTypeEnum $delivery_type
 * @property OrderPaymentMethodEnum $payment_method
 * @property float $shipping_price - default: 0.00
 * @property int $user_id
 * @property int|null $address_id - Not null if delivery_type is OrderDeliveryTypeEnum::DELIVERY
 * @property int|null $delivery_point_id - Not null if delivery_type is OrderDeliveryTypeEnum::SELF_PICKUP
 * @property Carbon|null $delivered_at
 * @property int $status_id
 * @property int|null $installment_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read float $full_amount
 * @property User $user
 * @property Address|null $address
 * @property DeliveryPoint|null $deliveryPoint
 * @property OrderStatus $status
 * @property Installment|null $installment
 * @property OrderStatusHistory[] $statusHistories
 * @property OrderItem[] $orderItems
 */
class Order extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes;

    protected static array $sortables = ['id', 'user_id', 'address_id', 'status_id', 'installment_id', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_orders';

    protected $fillable = [
        'delivery_type',
        'payment_method',
        'shipping_price',
        'user_id',
        'address_id',
        'delivery_point_id',
        'status_id',
        'installment_id',
    ];

    protected $casts = [
        'delivery_type' => OrderDeliveryTypeEnum::class,
        'payment_method' => OrderPaymentMethodEnum::class,
    ];

    public function getFullAmountAttribute(): float
    {
        return $this->orderItems()->sum('price') + $this->shipping_price;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function deliveryPoint(): BelongsTo
    {
        return $this->belongsTo(DeliveryPoint::class, 'delivery_point_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class, 'installment_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
