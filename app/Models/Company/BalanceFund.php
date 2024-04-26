<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $amount
 * @property string $phone_number
 * @property int $external_transaction_id
 * @property string|null $terminal_id
 * @property string|null $merchant_id
 * @property string|null $card_number
 * @property Carbon $created_at
 */
class BalanceFund extends Model
{
    use TenantConnection;

    const UPDATED_AT = null;

    protected $fillable = [
        'amount',
        'phone_number',
        'external_transaction_id',
        'terminal_id',
        'merchant_id',
        'card_number',
    ];
}
