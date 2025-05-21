<?php

namespace App\Models\Company\Ecommerce;

use App\Enums\Company\PaymentStatusEnum;
use App\Models\Admin;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\OrderBySortable;
use App\Models\Company\TransactionType;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property float $amount
 * @property string|null $card_pan
 * @property PaymentStatusEnum $status
 * @property Carbon|null $paid_at
 * @property string|null $external_id
 * @property string $order_id
 * @property int $transaction_type_id
 * @property int $user_id
 * @property int|null $admin_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Admin|null $admin
 * @property User $user
 * @property Order $order
 * @property TransactionType $transactionType
 */
class Payment extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, TenantConnection;

    public static array $sortables = [
        'amount',
        'card_pan',
        'status',
        'paid_at',
        'external_id',
        'transaction_type_id',
        'user_id',
        'admin_id',
    ];

    protected $table = 'ecommerce_payments';

    protected $fillable = [
        'amount',
        'card_pan',
        'status',
        'paid_at',
        'external_id',
        'transaction_type_id',
        'user_id',
        'admin_id',
        'order_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'status' => PaymentStatusEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $record) {
            if ($record->paid_at === null && $record->status === PaymentStatusEnum::SUCCESS) {
                $record->paid_at = Carbon::now();
            }
            if (auth('company-admin')->check()) {
                $record->admin_id = auth('company-admin')->id();
            }
        });
    }

    public function cardPan(): Attribute
    {
        return Attribute::make(set: fn ($value) => substr_replace($value, '******', 6, 6));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class)->withTrashed();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class, 'admin_id', 'id');
    }

    public function canceled(): void
    {
        $this->updateOrFail(['status' => PaymentStatusEnum::CANCELED]);
    }
}
