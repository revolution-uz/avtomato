<?php

namespace App\Models\Company;

use App\Enums\Company\InstallmentStatusEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $installment_id
 * @property Installment $installment
 * @property int $customer_id
 * @property Customer $customer
 * @property int $card_id
 * @property Card $card
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
    ];

    protected $casts = [
        'paid_at' => 'date',
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
            $transaction->setInstallmentGraphData($transaction->amount);
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

    public function setInstallmentGraphData(float $amount)
    {
        $installmentGraph = InstallmentGraph::query()->where('installment_id', $this->installment_id)
            ->where(DB::raw('amount - paid_amount'), '>', 0)
            ->orderBy('starts_in')->firstOrFail();
        /** @var InstallmentGraph $installmentGraph */
        $min_amount = min($amount, $installmentGraph->debt_amount);
        $installmentGraph->paid_amount += $min_amount;
        $amount -= $min_amount;
        if (! $installmentGraph->debt_amount) {
            $installmentGraph->status = InstallmentStatusEnum::CLOSED;
        }
        $installmentGraph->save();

        return $amount > 0 ? $this->setInstallmentGraphData($amount) : true;
    }
}
