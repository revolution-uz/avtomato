<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstallmentCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id',
        'card_id',
    ];
}
