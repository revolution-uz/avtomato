<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
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
 * @property int $starts_in
 * @property int $next_pay_date
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 */
class Installment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dateFormat = 'U';

    protected $fillable = [
        'user_id',
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
