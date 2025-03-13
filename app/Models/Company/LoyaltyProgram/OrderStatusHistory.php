<?php

namespace App\Models\Company\LoyaltyProgram;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class OrderStatusHistory extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_order_status_history';

    protected $fillable = [
        'order_id',
        'admin_id',
        'from_status',
        'to_status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
