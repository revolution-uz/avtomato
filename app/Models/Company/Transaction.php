<?php

namespace App\Models\Company;

use App\Clients\IcanClient;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Events\TelegramEvents\TransactionTelegramMessageEvent;
use App\Jobs\CreateOrUpdateInstallmentInUniredJob;
use App\Jobs\SendTransactionSmsJob;
use App\Services\AtmosService;
use App\Services\IcanService;
use App\Services\UniredService;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int|null $branch_id
 * @property Branch|null $branch
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
 * @property bool $is_initial_fee
 * @property int $admin_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read  CompanyAdmin $admin
 * @property Carbon|null $paid_at
 * @property TransactionStatusEnum $status
 * @property int|null $ican_id
 *
 * @method static Builder onlySuccess()
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

            if ($transaction->installment->ican_id) {
                $debtAmount = app(IcanService::class)->getInstallmentDebtAmount($transaction->installment->ican_id);

                if ($debtAmount < $transaction->amount) {
                    $transaction->reverse();

                    return false;
                }

                $icanService = new IcanService(new IcanClient);
                $response = $icanService->createTransaction($transaction);
                $transaction->ican_id = $response->id;
            }
        });

        static::created(function (self $transaction) {
            $installment = $transaction->installment;

            if (! $transaction->is_initial_fee) {
                $installment->increment('paid_amount', $transaction->amount);
            }
            if ($installment->wholeDebtAmount <= 1) {
                $installment->update(['status' => InstallmentStatusEnum::CLOSED]);
            }
            if ($transaction->transactionType->is_system) {
                CreateOrUpdateInstallmentInUniredJob::dispatch($transaction->installment_id);
            }

            SendTransactionSmsJob::dispatch($transaction->id);
            event(new TransactionTelegramMessageEvent($transaction));
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
        return $this->belongsTo(CompanyAdmin::class, 'admin_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function canceled(): void
    {
        $this->updateOrFail(['status' => TransactionStatusEnum::CANCELED]);
    }

    public function scopeOnlySuccess($query)
    {
        return $query->where('status', TransactionStatusEnum::SUCCESS);
    }

    public function reverse()
    {
        $atmosService = app(AtmosService::class);
        $uniredService = app(UniredService::class);

        if ($this->transaction_type_id == TransactionType::ATMOS_WITH_CARD) {
            $atmosService->reverseCardPayment($this);
        } elseif ($this->transaction_type_id == TransactionType::ATMOS_WITH_PINFL) {
            $atmosService->reversePinflPayment($this);
        } elseif ($this->transaction_type_id === TransactionType::UNIRED_WITH_PINFL) {
            $uniredService->reversePayment($this);
        }

        $this->status = TransactionStatusEnum::CANCELED;

        return $this->saveOrFail();
    }
}
