<?php

namespace App\Models\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * SbpTransaction model
 *
 * @property int $id
 * @property int $terminal_id
 * @property string $ext_id
 * @property float $amount
 * @property array|null $credit
 * @property array|null $debit
 * @property array|null $sender
 * @property array|null $receiver
 * @property float|null $currency
 * @property Carbon|null $payment_updated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property TransactionAttemptStatusEnum $status
 */
class SbpTransaction extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $fillable = [
        'terminal_id',
        'ext_id',
        'amount',
        'credit',
        'debit',
        'sender',
        'receiver',
        'currency',
        'payment_updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:30',
        'credit' => 'array',
        'debit' => 'array',
        'sender' => 'array',
        'receiver' => 'array',
        'status' => TransactionAttemptStatusEnum::class,
        'payment_updated_at' => 'datetime',
    ];
}
