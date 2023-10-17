<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $installment_id
 * @property float $amount
 * @property float $paid_amount
 * @property int $overdue_days
 * @property int $status
 * @property Carbon|null $starts_in
 */
class InstallmentGraph extends Model
{
    use HasFactory;
    use TenantConnection;

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
    ];
}
