<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $installment_id
 * @property int $card_id
 * @property Carbon|null $deleted_at
 */
class InstallmentCard extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'installment_id',
        'card_id',
    ];
}
