<?php

namespace App\Models\Company;

use App\Enums\Company\SmsMessageCustomerTypeEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $phone
 * @property int|null $ext_id
 * @property int|null $customer_id
 * @property Customer|null $customer
 * @property int|null $installment_id
 * @property Installment|null $installment
 * @property string $message
 * @property SmsMessageStatusEnum $status
 * @property SmsMessageCustomerTypeEnum $customer_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SmsMessage extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'phone', 'message'];

    protected $fillable = [
        'phone',
        'message',
        'status',
        'ext_id',
        'installment_id',
        'customer_id',
        'customer_type',
        'updated_at',
    ];

    protected $casts = [
        'status' => SmsMessageStatusEnum::class,
        'customer_type' => SmsMessageCustomerTypeEnum::class,
    ];

    protected $attributes = [
        'customer_type' => SmsMessageCustomerTypeEnum::OTHERS,
    ];

    public function charge(): MorphOne
    {
        return $this->morphOne(BalanceCharge::class, 'chargeable');
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
