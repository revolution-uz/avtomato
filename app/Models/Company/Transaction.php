<?php

namespace App\Models\Company;

use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\SendTransactionSmsJob;
use App\Traits\LogsActivityDirtyOnlyTrait;
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
 * @property string|null $card_pan
 * @property int $transaction_type_id
 * @property TransactionType $transactionType
 * @property string|null $external_id
 * @property float $amount
 * @property int $admin_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read  CompanyAdmin $admin
 * @property Carbon|null $paid_at
 */
class Transaction extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'installment_id', 'customer_id', 'card_id', 'transaction_type_id', 'amount', 'on_created'];

    protected $fillable = [
        'installment_id',
        'customer_id',
        'card_id',
        'transaction_type_id',
        'external_id',
        'amount',
        'admin_id',
        'paid_at',
        'card_pan',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $transaction) {
            if ($transaction->paid_at === null) {
                $transaction->paid_at = Carbon::now();
            }
            if (auth()->check()) {
                $transaction->admin_id = auth()->user()->id;
            }
        });

        static::created(function (self $transaction) {
            Installment::query()
                ->where('id', $transaction->installment_id)
                ->increment('paid_amount', $transaction->amount);
            if ($transaction->card_pan) {
                dispatch(new SendTransactionSmsJob($transaction->id))->onQueue('sms');
            }
            $installment = Installment::findOrFail($transaction->installment_id);
            if ($installment->debtAmount <= 1) {
                $installment->update(['status' => InstallmentStatusEnum::CLOSED]);
            }
        });

        static::deleted(function (self $transaction) {
            Installment::query()
                ->where('id', $transaction->installment_id)
                ->decrement('paid_amount', $transaction->amount);
        });
    }

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

    public function transactionType(): BelongsTo
    {
        return $this->belongsTo(TransactionType::class)->withTrashed();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class, 'admin_id', 'id');
    }
}
