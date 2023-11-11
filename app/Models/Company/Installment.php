<?php

namespace App\Models\Company;

use App\Enums\Company\InstallmentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property Customer $customer
 * @property int $branch_id
 * @property Branch $branch
 * @property int $tariff_id
 * @property int $type
 * @property Tariff $tariff
 * @property float $amount
 * @property float $prepayment
 * @property string $description
 * @property bool $sms_notify
 * @property bool $any_card_charge
 * @property bool $is_blacklist
 * @property InstallmentStatusEnum $status
 * @property int $period
 * @property string $plum_id
 * @property Carbon|null $starts_in
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Installment extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tariff_id',
        'type',
        'amount',
        'prepayment',
        'description',
        'sms_notify',
        'any_card_charge',
        'is_blacklist',
        'status',
        'period',
        'plum_id',
        'starts_in',
        'next_pay_date',
    ];

    protected $casts = [
        'starts_in' => 'date:d-m-Y',
        'next_pay_date' => 'date:d-m-Y',
        'status' => InstallmentStatusEnum::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
