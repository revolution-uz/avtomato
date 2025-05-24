<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Models\Company\CompanyAdmin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class OrderStatusHistory
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $admin_id
 * @property OrderStatusEnum $from_status
 * @property OrderStatusEnum $to_status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Order $order
 * @property CompanyAdmin|null $admin
 */
class OrderStatusHistory extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_order_status_histories';

    protected $casts = [
        'from_status' => OrderStatusEnum::class,
        'to_status' => OrderStatusEnum::class,
    ];

    protected $fillable = [
        'order_id',
        'admin_id',
        'from_status',
        'to_status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class)->withTrashed();
    }
}
