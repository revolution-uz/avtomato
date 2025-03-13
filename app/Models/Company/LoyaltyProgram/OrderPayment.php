<?php

namespace App\Models\Company\LoyaltyProgram;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPayment extends Model
{
    use SoftDeletes;

    protected $table = 'loyalty_program_order_payment';

    protected $fillable = [
        'order_id',
        'admin_id',
        'payment_method',
        'status',
        'amount',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
