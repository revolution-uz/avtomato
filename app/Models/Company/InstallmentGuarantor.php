<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property int $customer_id
 * @property bool $auto_charge
 * @property bool $any_card_charge
 * @property int $status
 * @property string $myuzkard_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class InstallmentGuarantor extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'installment_id',
        'customer_id',
        'auto_charge',
        'any_card_charge',
        'status',
        'myuzkard_id',
    ];
}
