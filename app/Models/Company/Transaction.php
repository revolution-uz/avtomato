<?php

namespace App\Models\Company;

use App\Enums\Company\CardTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Events\TelegramEvents\TransactionTelegramMessageEvent;
use App\Jobs\CreateIcanTransactionJob;
use App\Jobs\CreateOrUpdateInstallmentInUniredJob;
use App\Jobs\SendTransactionSmsJob;
use App\Services\AtmosService;
use App\Services\UniredService;
use App\Services\User\BalanceService;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Transaction
 *
 * @property int $id
 * @property int|null $branch_id
 * @property int $installment_id
 * @property int $customer_id
 * @property int|null $card_id
 * @property string|null $card_pan
 * @property string|null $card_owner
 * @property string|null $card_phone
 * @property int $transaction_type_id
 * @property string|null $external_id
 * @property float $amount
 * @property bool $is_initial_fee
 * @property int|null $admin_id
 * @property Carbon|null $paid_at
 * @property TransactionStatusEnum $status
 * @property int|null $ican_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Installment $installment
 * @property Customer $customer
 * @property Card|null $card
 * @property TransactionType $transactionType
 * @property CompanyAdmin|null $admin
 * @property Branch|null $branch
 * @property-read CardTypeEnum|null $cardType
 *
 * @method static Builder onlySuccess()
 * @method static Builder withoutDiscount()
 */
class Transaction extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, TenantConnection;

    public static array $sortables = ['id', 'installment_id', 'customer_id', 'card_id', 'transaction_type_id', 'amount', 'on_created', 'is_initial_fee'];

    protected $fillable = [
        'installment_id',
        'branch_id',
        'customer_id',
        'card_id',
        'card_owner',
        'card_phone',
        'transaction_type_id',
        'external_id',
        'amount',
        'admin_id',
        'paid_at',
        'card_pan',
        'is_initial_fee',
        'status',
        'ican_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => TransactionStatusEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $transaction) {
            $transaction->paid_at ??= Carbon::now();

            if (auth('company-admin')->check()) {
                $transaction->admin_id = auth('company-admin')->id();
            }
        });

        static::created(function (self $transaction) {
            $installment = $transaction->installment;

            if (! $transaction->is_initial_fee) {
                $installment->increment('paid_amount', $transaction->amount);
            }
            if ($transaction->transactionType->is_system) {
                $transaction->computeCommission();
            }
            if ($installment->ican_id) {
                CreateIcanTransactionJob::dispatch($transaction->id);
            }
            if ($installment->wholeDebtAmount <= 1) {
                $installment->update(['status' => InstallmentStatusEnum::CLOSED]);
            }
            if ($transaction->transactionType->is_local) {
                SendTransactionSmsJob::dispatch($transaction->id);
                event(new TransactionTelegramMessageEvent($transaction));
            }
            if ($installment->unired_id && $transaction->transaction_type_id !== TransactionType::UNIRED_WITH_PINFL) {
                CreateOrUpdateInstallmentInUniredJob::dispatch($transaction->installment_id);
            }
        });

        static::updating(function (self $transaction) {
            if (
                $transaction->isDirty('status')
                and $transaction->getOriginal('status') === TransactionStatusEnum::SUCCESS
                and $transaction->status === TransactionStatusEnum::CANCELED
                and ! $transaction->is_initial_fee
            ) {
                Installment::query()->where('id', $transaction->installment_id)->decrement('paid_amount', $transaction->amount);
            }
        });

        static::deleted(function (self $transaction) {
            if (! $transaction->is_initial_fee) {
                Installment::query()->where('id', $transaction->installment_id)->decrement('paid_amount', $transaction->amount);
            }
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
        return $this->belongsTo(CompanyAdmin::class)->withTrashed();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeOnlySuccess($query)
    {
        return $query->where('status', TransactionStatusEnum::SUCCESS);
    }

    public function scopeWithoutDiscount($query)
    {
        return $query->whereNot('transaction_type_id', TransactionType::DISCOUNT);
    }

    public function reverse()
    {
        if ($this->status == TransactionStatusEnum::CANCELED) {
            return true;
        }
        $atmosService = app(AtmosService::class);
        $uniredService = app(UniredService::class);
        $isCancelled = true;

        if ($this->transaction_type_id == TransactionType::ATMOS_WITH_CARD) {
            $isCancelled = $atmosService->reverseCardPayment($this->external_id);
        } elseif ($this->transaction_type_id == TransactionType::ATMOS_WITH_PINFL) {
            $isCancelled = $atmosService->reversePinflPayment($this->id);
        } elseif ($this->transaction_type_id === TransactionType::UNIRED_WITH_PINFL) {
            $isCancelled = $uniredService->reversePayment($this->external_id);
        }
        if ($isCancelled) {
            $this->status = TransactionStatusEnum::CANCELED;

            return $this->saveOrFail();
        }

        return $isCancelled;
    }

    protected function cardType(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => CardTypeEnum::getCardType($this->card_pan),
        );
    }

    protected function cardPan(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?? $this->card->pan ?? null,
        );
    }

    private function computeCommission(): void
    {
        if ($featureEnum = TransactionType::getFeature($this->transaction_type_id)) {
            if ($feature = Feature::query()
                ->where('name', $featureEnum)
                ->first()) {
                $commission = max(0, CardTypeEnum::getCardType($this->card_pan) === CardTypeEnum::HUMO ? $feature->humo_percent : $feature->uzcard_percent);
                app(BalanceService::class)->chargeAmount($featureEnum, $this, $this->amount * $commission);
            }
        }
    }
}
