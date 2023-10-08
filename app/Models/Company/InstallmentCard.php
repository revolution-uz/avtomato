<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $installment_id
 * @property int $card_id
 */
class InstallmentCard extends Model
{
    use HasFactory;
    use TenantConnection;

    protected $fillable = [
        'installment_id',
        'card_id',
    ];
}
