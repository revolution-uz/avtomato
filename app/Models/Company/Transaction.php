<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property int $customer_id
 * @property int $card_id
 * @property int $transaction_type_id
 * @property int $myuzkard_id
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Transaction extends Model
{
    use HasFactory;
    use TenantConnection;

    protected $fillable = [
        'installment_id',
        'customer_id',
        'card_id',
        'transaction_type_id',
        'myuzkard_id',
        'amount',
    ];
}
