<?php

namespace App\Models\Company;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $branch_id
 * @property int $admin_id
 * @property int $tariff_id
 * @property float $percent
 * @property float $amount
 * @property float $installment_amount
 * @property float $paid_amount
 * @property float $initial_fee
 * @property string $description
 * @property bool $sms_notify
 * @property bool $any_card_charge
 * @property bool $is_blacklist
 * @property bool $auto_pay
 * @property int $period
 * @property string $plum_id
 * @property Carbon|null $starts_in
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $canceled_at
 * @property int|null $card_after_due_days
 * @property int|null $pinfl_after_due_days
 * @property int|null $guarantor_after_due_days
 * @property int|null $guarantor_pinfl_after_due_days
 * @property int|null $overdue_days
 * @property string $cancellation_details
 * @property InstallmentTypeEnum $type
 * @property InstallmentStatusEnum $status
 * @property Branch $branch
 * @property Tariff $tariff
 * @property Customer $customer
 * @property Card[]|Collection<int, Card> $cards
 * @property Attachment[] $documents
 * @property InstallmentComment[]|Collection<int, InstallmentComment> $comments
 * @property Customer[]|Collection<int, Customer> $guarantors
 * @property Transaction[]|Collection<int, Transaction> $transactions
 * @property InstallmentGuarantor[] $guarantorList
 * @property-read  CompanyAdmin $admin
 * @property-read float $debtAmount
 * @property-read float $monthlyPayment
 * @property-read float $is_active
 * @property-read float $is_fully_paid
 * @property-read Carbon $nextPaymentDate
 * @property mixed $wholeDebtAmount
 * @property float $portion
 *
 * @method static Builder whereNextPayDate(string $operator, Carbon $date)
 */
class Installment extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'customer_id', 'tariff_id', 'amount', 'created_at', 'starts_in'];

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tariff_id',
        'admin_id',
        'type',
        'amount',
        'initial_fee',
        'percent',
        'description',
        'sms_notify',
        'any_card_charge',
        'is_blacklist',
        'status',
        'period',
        'plum_id',
        'canceled_at',
        'cancellation_details',
        'starts_in',
        'card_after_due_days',
        'pinfl_after_due_days',
        'guarantor_after_due_days',
        'guarantor_pinfl_after_due_days',
        'paid_amount',
    ];

    protected $casts = [
        'canceled_at' => 'date:d-m-Y',
        'starts_in' => 'date:d-m-Y',
        'status' => InstallmentStatusEnum::class,
        'type' => InstallmentTypeEnum::class,
    ];

    public static function getDebtAmountRaw(): \Illuminate\Contracts\Database\Query\Expression
    {
        return DB::raw('(amount - initial_fee) * ((percent + 100) / 100) - paid_amount');
    }

    public static function getInstallmentAmountRaw(): \Illuminate\Contracts\Database\Query\Expression
    {
        return DB::raw('(amount - initial_fee) * (percent + 100) / 100');
    }

    public static function getProfitAmountRaw(): \Illuminate\Contracts\Database\Query\Expression
    {
        return DB::raw('(amount - initial_fee - paid_amount) * percent / 100');
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class)->withTrashed();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function cards(): BelongsToMany
    {
        return $this
            ->belongsToMany(Card::class, 'installment_cards', 'installment_id', 'card_id')
            ->whereNull('installment_cards.deleted_at')
            ->select('cards.*');
    }

    public function guarantors(): BelongsToMany
    {
        return $this
            ->belongsToMany(Customer::class, 'installment_guarantors', 'installment_id', 'customer_id')
            ->whereNull('installment_guarantors.deleted_at')
            ->select('customers.*')
            ->withTimestamps();
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'installment_id');
    }

    public function guarantorList(): HasMany
    {
        return $this->hasMany(InstallmentGuarantor::class, 'installment_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(InstallmentComment::class, 'installment_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id')->withTrashed();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(CompanyAdmin::class, 'admin_id')->withTrashed();
    }

    public function initialFeeTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'installment_id')
            ->where('status', TransactionStatusEnum::SUCCESS)
            ->where('is_initial_fee', true);
    }

    public function autoPay(): Attribute
    {
        return Attribute::make(get: fn () => $this->type->value & InstallmentTypeEnum::WITH_CARD->value);
    }

    public function wholeDebtAmount(): Attribute
    {
        return Attribute::make(get: fn () => $this->installment_amount - $this->paid_amount);
    }

    public function debtAmount(): Attribute
    {
        return Attribute::make(get: fn () => $this->monthlyPayment * min(max(ceil($this->starts_in->diffInMonths()), 0), $this->period) - $this->paid_amount);
    }

    public function isAvailableCustomerCard(): bool
    {
        return $this->isAvailablePaymentDate(intval($this->card_after_due_days !== null ? $this->card_after_due_days : CompanySettings::getValue(CompanySettingsEnum::CARD_AFTER_DUE_DAYS)));
    }

    public function isAvailableCustomerPinfl(): bool
    {
        return $this->isAvailablePaymentDate(intval($this->pinfl_after_due_days !== null ? $this->pinfl_after_due_days : CompanySettings::getValue(CompanySettingsEnum::PINFL_AFTER_DUE_DAYS)));
    }

    public function isAvailableGuarantorCard(): bool
    {
        return $this->isAvailablePaymentDate(intval($this->guarantor_after_due_days !== null ? $this->guarantor_after_due_days : CompanySettings::getValue(CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS)));
    }

    public function isAvailableGuarantorPinfl(): bool
    {
        return $this->isAvailablePaymentDate(intval($this->guarantor_pinfl_after_due_days !== null ? $this->guarantor_pinfl_after_due_days : CompanySettings::getValue(CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS)));
    }

    public function nextPaymentDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->starts_in->addMonths(floor($this->paid_amount / $this->monthlyPayment))->setTime(0, 0)
        );
    }

    public function monthlyPayment(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->installment_amount / $this->period, 2)
        );
    }

    public function installmentAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => round(($this->amount - $this->initial_fee) * $this->portion, 2)
        );
    }

    public function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status == InstallmentStatusEnum::ACTIVE
        );
    }

    public function portion(): Attribute
    {
        return Attribute::make(get: fn () => ($this->percent + 100) / 100);
    }

    public function isPaidInitialFee(): bool
    {
        return (float) $this->transactions()->where('is_initial_fee', true)->where('status', TransactionStatusEnum::SUCCESS)->sum('amount') == $this->initial_fee;
    }

    public function isFullyPaid(): Attribute
    {
        return Attribute::make(get: fn () => $this->paid_amount >= $this->installment_amount);
    }

    public function overdueDays(): Attribute
    {
        return Attribute::make(get: fn () => (int) $this->nextPaymentDate->diffInDays(Carbon::today()));
    }

    public function scopeWhereHasDebt($query)
    {
        return $query->where(DB::raw('(amount - initial_fee) * (percent + 100) / 100 - paid_amount'), '>', 0);
    }

    public function scopeWhereNextPayDate($query, string $expression, Carbon $date)
    {
        return $query->where(DB::raw("starts_in + (FLOOR(paid_amount / ROUND(((amount - initial_fee) * (percent / 100 + 1) / period)::numeric, 2))::text || ' month')::interval"), $expression, $date);
    }

    public function scopeCountId($query)
    {
        return $query->count('id');
    }

    public function scopeSumDebt($query)
    {
        return $query->sum(self::getDebtAmountRaw());
    }

    public function scopeSumInstallmentAmount($query)
    {
        return $query->sum(self::getInstallmentAmountRaw());
    }

    public function scopeSumProfit($query)
    {
        return $query->sumCustomRaw(self::getProfitAmountRaw());
    }

    private function isAvailablePaymentDate(int $delay): bool
    {
        return $this->nextPaymentDate->addDays($delay) <= Carbon::now();
    }
}
