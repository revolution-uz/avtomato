<?php

namespace App\Models\Company;

use App\Enums\Company\InstallmentStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property float $amount
 * @property float $debt_amount
 * @property float $paid_amount
 * @property int $overdue_days
 * @property InstallmentStatusEnum $status
 * @property Installment $installment
 * @property Carbon|null $starts_in
 */
class InstallmentGraph extends Model
{
    use HasFactory, TenantConnection;

    protected $fillable = [
        'installment_id',
        'amount',
        'paid_amount',
        'overdue_days',
        'status',
        'starts_in',
    ];

    protected $casts = [
        'starts_in' => 'date:d-m-Y',
        'status' => InstallmentStatusEnum::class,
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function getDebtAmount(): Attribute
    {
        return Attribute::make(get: fn () => $this->amount - $this->paid_amount);
    }
}
