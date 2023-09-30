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
 * @property double $amount
 * @property string $description
 * @property boolean $sms_notify
 * @property boolean $auto_charge
 * @property boolean $any_card_charge
 * @property boolean $is_blacklist
 * @property int $status
 * @property int $period
 * @property string $myuzkard_id
 * @property int $starts_in
 * @property int $next_pay_date
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
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
