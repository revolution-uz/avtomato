<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\OrderPaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class OrderPayment extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_order_payment';

    protected $fillable = [
        'order_id',
        'admin_id',
        'payment_method',
        'status',
        'amount',
    ];

    protected $casts = [
        'status' => OrderPaymentStatusEnum::class,
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
