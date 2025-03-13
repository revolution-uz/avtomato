<?php

namespace App\Models\Company\LoyaltyProgram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

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
