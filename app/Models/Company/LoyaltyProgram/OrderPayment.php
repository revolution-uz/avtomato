<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgram\OrderPaymentStatusEnum;
use App\Models\Company\CompanyAdmin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class OrderPayment
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $admin_id
 * @property int $payment_method
 * @property OrderPaymentStatusEnum $status
 * @property float $amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Order $order
 * @property CompanyAdmin|null $admin
 */
class OrderPayment extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_order_payments';

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

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class)->withTrashed();
    }
}
