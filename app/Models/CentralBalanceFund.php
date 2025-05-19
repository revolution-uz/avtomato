<?php

namespace App\Models;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property int $amount
 * @property string|null $phone_number
 * @property int|null $external_transaction_id
 * @property string|null $terminal_id
 * @property string|null $merchant_id
 * @property string|null $card_pan
 * @property int $company_id
 * @property int $balance_fund_id
 * @property Carbon $created_at
 */
class CentralBalanceFund extends Model
{
    use CentralConnection, HasFactory, LogsActivityDirtyOnlyTrait;

    const UPDATED_AT = null;

    protected $fillable = [
        'amount',
        'phone_number',
        'external_transaction_id',
        'terminal_id',
        'merchant_id',
        'card_pan',
        'company_id',
        'balance_fund_id',
        'created_at',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
