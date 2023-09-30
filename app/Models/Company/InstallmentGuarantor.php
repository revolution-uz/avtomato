<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentGuarantor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dateFormat = 'U';

    protected $fillable = [
        'installment_id',
        'customer_id',
        'card_id',
        'auto_charge',
        'any_card_charge',
        'status',
    ];
}
