<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentGuarantors extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'customer_id',
        'card_id',
        'auto_charge',
        'any_card_charge',
        'status',
    ];
}
