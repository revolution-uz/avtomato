<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
