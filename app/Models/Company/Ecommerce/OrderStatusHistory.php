<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $from_status_id
 * @property int $to_status_id
 * @property int $admin_id
 * @property int $order_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Admin $admin
 * @property-read OrderStatus $fromStatus
 * @property-read OrderStatus $toStatus
 * @property-read Order $order
 */
class OrderStatusHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ecommerce_order_status_histories';

    protected $fillable = [
        'from_status_id',
        'to_status_id',
        'admin_id',
        'order_id',
    ];

    // Optional: Define relationships if needed
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function fromStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'from_status_id');
    }

    public function toStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'to_status_id');
    }
}
