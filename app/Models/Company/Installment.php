<?php

namespace App\Models\Company;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $branch_id
 * @property int $admin_id
 * @property int $tariff_id
 * @property float $percent
 * @property float $amount
 * @property float $paid_amount
 * @property float $initial_fee
 * @property string $description
 * @property bool $sms_notify
 * @property bool $any_card_charge
 * @property bool $is_blacklist
 * @property bool $withdrawn_auto_charge
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
 * @property string $cancellation_details
 * @property InstallmentTypeEnum $type
 * @property InstallmentStatusEnum $status
 * @property Branch $branch
 * @property Tariff $tariff
 * @property Customer $customer
 * @property Card[] $cards
 * @property Attachment[] $documents
 * @property InstallmentComment[]|Collection<int, InstallmentComment> $comments
 * @property Customer[]|Collection<int, Customer> $guarantors
 * @property Transaction[] $transactions
 * @property InstallmentGuarantor[] $guarantorList
 * @property-read  CompanyAdmin $admin
 * @property-read float $debtAmount
 * @property-read float $monthlyPayment
 * @property-read Carbon $nextPaymentDate
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
        'withdrawn_auto_charge',
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

    public function autoPay(): Attribute
    {
        return Attribute::make(get: fn () => $this->type == InstallmentTypeEnum::WITH_CARD);
    }

    public function debtAmount(): Attribute
    {
        return Attribute::make(get: fn () => ($this->amount - $this->initial_fee) * ($this->percent + 100) / 100 * ceil($this->starts_in->diffInMonths()) / $this->period - $this->paid_amount
        );
    }

    public function isAvailableCustomerCard()
    {
        $card_after_due_days = intval($this->card_after_due_days !== null ? $this->card_after_due_days : CompanySettings::query()->where('name', CompanySettingsEnum::CARD_AFTER_DUE_DAYS)->firstOrFail()->value);

        return $this->nextPaymentDate->addDays($card_after_due_days) <= Carbon::today();
    }

    public function isAvailableCustomerPinfl()
    {
        $pinfl_after_due_days = intval($this->pinfl_after_due_days !== null ? $this->pinfl_after_due_days : CompanySettings::query()->where('name', CompanySettingsEnum::PINFL_AFTER_DUE_DAYS)->firstOrFail()->value);

        return $this->nextPaymentDate->addDays($pinfl_after_due_days) <= Carbon::today();
    }

    public function isAvailableGurantorCard()
    {
        $guarantor_after_due_days = intval($this->guarantor_after_due_days !== null ? $this->guarantor_after_due_days : CompanySettings::query()->where('name', CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS)->firstOrFail()->value);

        return $this->nextPaymentDate->addDays($guarantor_after_due_days) <= Carbon::today();
    }

    public function isAvailableGuarantorPinfl()
    {
        $guarantor_pinfl_after_due_days = intval($this->guarantor_pinfl_after_due_days !== null ? $this->guarantor_pinfl_after_due_days : CompanySettings::query()->where('name', CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS)->firstOrFail()->value);

        return $this->nextPaymentDate->addDays($guarantor_pinfl_after_due_days) <= Carbon::today();
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
            get: fn () => round(($this->amount - $this->initial_fee) * ($this->percent + 100) / 100 / $this->period, 2)
        );
    }
}
