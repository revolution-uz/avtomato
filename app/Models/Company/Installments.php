<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installments extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tariff_id',
        'card_id',
        'smsNotify',
        'autoCharge',
        'anyCardCharge',
        'isBlacklist',
        'status',
    ];
}
