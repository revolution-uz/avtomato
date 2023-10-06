<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $installment_id
 * @property int $card_id
 */
class InstallmentCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_id',
        'card_id',
    ];
}
