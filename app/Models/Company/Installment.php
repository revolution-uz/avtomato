<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $branch_id
 * @property int $tariff_id
 * @property float $amount
 * @property string $description
 * @property bool $sms_notify
 * @property bool $auto_charge
 * @property bool $any_card_charge
 * @property bool $is_blacklist
 * @property int $status
 * @property int $period
 * @property string $myuzkard_id
 * @property Carbon|null $starts_in
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Installment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tariff_id',
        'amount',
        'description',
        'sms_notify',
        'auto_charge',
        'any_card_charge',
        'is_blacklist',
        'status',
        'period',
        'myuzkard_id',
        'starts_in',
        'next_pay_date',
    ];
}
