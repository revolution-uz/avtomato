<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $installment_id
 * @property int $card_id
 * @property bool $auto_charge
 * @property string $deleted_at
 */
class InstallmentCard extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'installment_id',
        'card_id',
        'auto_charge',
    ];
}
