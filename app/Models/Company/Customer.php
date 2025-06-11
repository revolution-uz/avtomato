<?php

namespace App\Models\Company;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CustomerTypeEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\GenderEnum;
use App\Models\CentralCustomer;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string|null $pinfl
 * @property string|null $tin
 * @property string|null $passport_series
 * @property string|null $passport_number
 * @property Carbon|null $passport_given_date
 * @property Carbon|null $passport_expiration_date
 * @property string|null $passport_given_place
 * @property string|null $passport_citizenship
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property Carbon|null $birth_date
 * @property string|null $birth_place
 * @property GenderEnum|null $gender
 * @property-read float $debt_amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Attachment[] $attachments
 * @property Installment[] $installments
 * @property Installment[] $guaranteedInstallments
 * @property Address[]|Collection<int,Address> $addresses
 * @property Phone[]|Collection<int,Phone> $phones
 * @property Card[]|Collection<int,Card> $cards
 * @property Tag[]|Collection<int, Tag> $tags
 * @property ScoringRequest[]|Collection<int,ScoringRequest> $scoringRequests
 * @property CustomerTypeEnum $type
 * @property-read string $full_name
 * @property-read string $passport
 * @property-read Carbon|null $unired_cards_last_updated_at
 *
 * @method static Builder|static query()
 * @method static Customer find(int $id)
 * @method static Customer findOrFail(int $id)
 * @method static Customer first()
 * @method static Customer firstOrFail()
 * @method Customer saveOrFail(array $array = [])
 */
class Customer extends Model
{
    use HasApiTokens, HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'pinfl', 'passport_series', 'passport_number', 'first_name', 'last_name', 'birth_date'];

    protected $fillable = [
        'pinfl',
        'tin',
        'passport_series',
        'passport_number',
        'passport_given_date',
        'passport_expiration_date',
        'passport_given_place',
        'passport_citizenship',
        'first_name',
        'last_name',
        'middle_name',
        'birth_date',
        'birth_place',
        'gender',
        'type',
    ];

    protected $casts = [
        'passport_given_date' => 'date',
        'passport_expiration_date' => 'date',
        'birth_date' => 'date',
        'gender' => GenderEnum::class,
        'type' => CustomerTypeEnum::class,
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $customer) {
            ScoringRequest::query()
                ->whereNull('customer_id')
                ->where(fn ($q) => $q
                    ->when($customer->pinfl, fn ($sq) => $sq
                        ->orWhere('request->pinfl', $customer->pinfl))
                    ->when($customer->passport_series && $customer->passport_number, fn ($sq) => $sq
                        ->orWhere('request->passport_series', $customer->passport_series)
                        ->orWhere('request->passport_number', $customer->passport_number)
                        ->orWhere('request->passport_id', $customer->passport)))
                ->update(['customer_id' => $customer->id]);
        });
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function phones(): MorphToMany
    {
        return $this->morphToMany(Phone::class, 'phoneable')
            ->wherePivotIn('type', PhoneTypeEnum::customerValues())
            ->withPivot(['id', 'type', 'comment', 'created_at', 'updated_at', 'installment_notifications']);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class)->where('cards.status', '!=', CardStatusEnum::INACTIVE);
    }

    public function scoringRequests(): HasMany
    {
        return $this->hasMany(ScoringRequest::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function guaranteedInstallments(): BelongsToMany
    {
        return $this
            ->belongsToMany(Installment::class, 'installment_guarantors', 'customer_id', 'installment_id')
            ->wherePivotNull('deleted_at');
    }

    public function getUniredCards()
    {
        return $this->getCentralCustomer()?->uniredCards;
    }

    public function fullName(): Attribute
    {
        return Attribute::make(get: fn () => $this->last_name . ' ' . $this->first_name);
    }

    public function activeCards()
    {
        return $this->cards()->where('status', CardStatusEnum::ACTIVE);
    }

    public function getDebtAmountAttribute(): float
    {
        return $this->installments()->where('status', InstallmentStatusEnum::ACTIVE)->get()->sum('debtAmount');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'customer_tags');
    }

    public function passport(): Attribute
    {
        return Attribute::make(get: fn () => $this->passport_series . $this->passport_number);
    }

    public function uniredCardsLastUpdatedAt(): Attribute
    {
        return Attribute::make(get: function () {
            $centralCustomer = $this->getCentralCustomer();

            return $centralCustomer
                ? max($centralCustomer->unired_humo_searched_at, $centralCustomer->unired_uzcard_searched_at)
                : null;
        });
    }

    public function getCentralCustomer(): ?CentralCustomer
    {
        return CentralCustomer::query()->firstWhere('pinfl', $this->pinfl);
    }

    public function getOrCreateCentralCustomer()
    {
        return CentralCustomer::query()->firstOrCreate([
            'pinfl' => $this->pinfl,
        ], [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
        ]);
    }
}
