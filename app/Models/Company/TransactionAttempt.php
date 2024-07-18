<?php

namespace App\Models\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property Installment $installment
 * @property int $customer_id
 * @property Customer $customer
 * @property int $card_id
 * @property Card $card
 * @property int $transaction_id
 * @property TransactionAttemptStatusEnum $status
 * @property Transaction $transaction
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TransactionAttempt extends Model
{
    use HasFactory, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'installment_id', 'customer_id', 'card_id', 'transaction_id', 'amount'];

    protected $fillable = [
        'installment_id',
        'customer_id',
        'card_id',
        'transaction_id',
        'amount',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => TransactionAttemptStatusEnum::class,
    ];

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function failed(): void
    {
        $this->updateOrFail(['status' => TransactionAttemptStatusEnum::FAILED]);
    }

    public function success(int $transaction_id): void
    {
        $this->updateOrFail(['status' => TransactionAttemptStatusEnum::SUCCESS, 'transaction_id' => $transaction_id]);
    }
}
