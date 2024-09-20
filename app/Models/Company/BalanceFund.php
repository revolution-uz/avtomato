<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $amount
 * @property string|null $phone_number
 * @property int|null $external_transaction_id
 * @property string|null $terminal_id
 * @property string|null $merchant_id
 * @property string|null $card_number
 * @property string|null $card_pan
 * @property int|null $admin_id
 * @property Carbon $created_at
 * @property CompanyAdmin $admin
 */
class BalanceFund extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'amount', 'phone_number', 'card_number', 'created_at'];

    const UPDATED_AT = null;

    protected $fillable = [
        'amount',
        'phone_number',
        'external_transaction_id',
        'terminal_id',
        'merchant_id',
        'card_number',
    ];

    protected $casts = [
        'amount' => 'integer',
        'external_transaction_id' => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class);
    }

    public function cardPan(): Attribute
    {
        return Attribute::make(get: fn () => substr_replace($this->card_number, '******', 6, 6));
    }
}
