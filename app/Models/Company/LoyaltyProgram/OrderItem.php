<?php

namespace App\Models\Company\LoyaltyProgram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $table = 'loyalty_program_order_item';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'discount_amount',
        'total_amount',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
} 