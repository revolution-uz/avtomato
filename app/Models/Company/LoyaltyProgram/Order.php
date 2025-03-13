<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\DiscountTypeEnum;
use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class Order extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_order';

    protected $fillable = [
        'user_id',
        'order_id',
        'admin_id',
        'status',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'total_discount_amount',
        'total_paid_amount',
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'discount_type' => DiscountTypeEnum::class,
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_discount_amount' => 'decimal:2',
        'total_paid_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }
}
