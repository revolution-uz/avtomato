<?php

namespace App\Models\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * SbpTransaction model
 *
 * @property int $id
 * @property int $terminal_id
 * @property string $ext_id
 * @property float $amount
 * @property array|null $cheque
 * @property array|null $exchange
 * @property array|null $qr
 * @property array|null $callback
 * @property Carbon|null $payment_updated_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property TransactionAttemptStatusEnum $status
 */
class SbpTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'terminal_id',
        'ext_id',
        'amount',
        'cheque',
        'exchange',
        'qr',
        'callback',
        'payment_updated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:30',
        'cheque' => 'array',
        'exchange' => 'array',
        'qr' => 'array',
        'callback' => 'array',
        'status' => TransactionAttemptStatusEnum::class,
        'payment_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
