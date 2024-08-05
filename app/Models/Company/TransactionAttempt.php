<?php

namespace App\Models\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Enums\Company\TransactionAttemptTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property TransactionAttemptTypeEnum $type
 * @property int $installment_id
 * @property Installment $installment
 * @property int $customer_id
 * @property Customer $customer
 * @property int $card_id
 * @property Card|null $card
 * @property int $transaction_id
 * @property TransactionAttemptStatusEnum $status
 * @property string $customer_type
 * @property int $transaction_type_id
 * @property TransactionType $transactionType
 * @property Transaction|null $transaction
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
        'type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => TransactionAttemptStatusEnum::class,
        'type' => TransactionAttemptTypeEnum::class,
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

    public function customerType(): Attribute
    {
        return Attribute::make(get: fn () => $this->customer_id === $this->installment->customer_id ? __('api.debtor') : __('api.guarantor'));
    }

    public function transactionTypeId(): Attribute
    {
        return Attribute::make(get: fn () => $this->card_id === null ? TransactionType::ATMOS_WITH_PINFL : TransactionType::ATMOS_WITH_CARD);
    }

    public function getTransactionTypeAttribute(): TransactionType
    {
        return TransactionType::query()->findOrFail($this->transaction_type_id);
    }
}
