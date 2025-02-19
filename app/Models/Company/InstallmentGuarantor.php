<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property Installment $installment
 * @property int $customer_id
 * @property Customer $customer
 * @property int|null $unired_id
 * @property bool $any_card_charge
 * @property string $plum_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class InstallmentGuarantor extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    protected $fillable = [
        'installment_id',
        'customer_id',
        'any_card_charge',
        'plum_id',
    ];

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
