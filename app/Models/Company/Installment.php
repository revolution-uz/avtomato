<?php

namespace App\Models\Company;

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
 * @property int $user_id
 * @property int $tariff_id
 * @property float $amount
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
 * @property Carbon|null $next_pay_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $canceled_at
 * @property string $cancellation_details
 * @property InstallmentTypeEnum $type
 * @property InstallmentStatusEnum $status
 * @property Branch $branch
 * @property Tariff $tariff
 * @property Customer $customer
 * @property Card[] $cards
 * @property InstallmentGraph[] $graphs
 * @property Attachment[] $documents
 * @property InstallmentComment[]|Collection<int, InstallmentComment> $comments
 * @property Customer[] $guarantors
 * @property Transaction[] $transactions
 * @property InstallmentGuarantor[] $guarantorList
 * @property-read  User $user
 */
class Installment extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'customer_id', 'tariff_id', 'amount', 'created_at', 'starts_in', 'next_pay_date'];

    protected $fillable = [
        'customer_id',
        'branch_id',
        'tariff_id',
        'type',
        'amount',
        'initial_fee',
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
        'next_pay_date',
        'withdrawn_auto_charge',
    ];

    protected $casts = [
        'canceled_at' => 'date:d-m-Y',
        'starts_in' => 'date:d-m-Y',
        'next_pay_date' => 'date:d-m-Y',
        'status' => InstallmentStatusEnum::class,
        'type' => InstallmentTypeEnum::class,
    ];

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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

    public function graphs(): HasMany
    {
        return $this->hasMany(InstallmentGraph::class, 'installment_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function autoPay(): Attribute
    {
        return Attribute::make(get: fn () => $this->type == InstallmentTypeEnum::WITH_CARD);
    }

    public function contactList(): array
    {
        $addresses = collect();
        $phones = collect();

        $collectCustomerData = function (Customer $customer, bool $isGuarantor) use (&$addresses, &$phones) {
            foreach ($customer->addresses as $address) {
                $addresses->push([
                    'address' => $address,
                    'customer' => $this->installmentCustomer($customer, $isGuarantor),
                ]);
            }

            foreach ($customer->phones as $phone) {
                $phones->push([
                    'phone' => $phone,
                    'customer' => $this->installmentCustomer($customer, $isGuarantor),
                ]);
            }
        };

        $collectCustomerData($this->customer, false);

        foreach ($this->guarantors as $guarantor) {
            $collectCustomerData($guarantor, true);
        }

        return [
            'addresses' => $addresses->values()->all(),
            'phones' => $phones->values()->all(),
        ];
    }

    public function customerList()
    {
        $customers = collect();
        $customers->push($this->installmentCustomer($this->customer, false));

        foreach ($this->guarantors as $guarantor) {
            $customers->push($this->installmentCustomer($guarantor, true));
        }

        return $customers->values()->all();
    }

    private function installmentCustomer(Customer $customer, bool $isGuarantor): array
    {
        return [
            'id' => $customer->id,
            'full_name' => $customer->full_name,
            'type' => $isGuarantor ? trans('api.guarantor') : trans('api.debtor'),
            'customer' => $customer,
        ];
    }
}
