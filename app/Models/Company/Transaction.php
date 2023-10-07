<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property int $customer_id
 * @property int $card_id
 * @property int $transaction_type_id
 * @property float $amount
 * @property int $on_created
 * @property int $created_at
 * @property int $updated_at
 */
class Transaction extends Model
{
    use HasFactory;
    use TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'installment_id',
        'customer_id',
        'card_id',
        'transaction_type_id',
        'amount',
        'on_created',
        'created_at',
        'updated_at',
    ];
}
