<?php

namespace App\Models\Company;

use App\DTOs\Atmos\Card\AtmosCardDTO;
use App\Enums\Company\CardStatusEnum;
use App\Helpers\CardHelper;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property Customer $customer
 * @property string $pan
 * @property string $number
 * @property string $owner
 * @property string $expiry
 * @property string $phone
 * @property CardStatusEnum $status
 * @property float $balance
 * @property string $external_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $card_type
 * @property-read bool $is_connected
 *
 * @method static Builder|static query()
 * @method static self find(int $id)
 * @method static self findOrFail(int $id)
 * @method static self first()
 * @method static self firstOrFail()
 * @method self whereNumber(int $cardNumber)
 * @method self saveOrFail(array $array = [])
 */
class Card extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    protected $fillable = [
        'customer_id',
        'pan',
        'number',
        'expiry',
        'phone',
        'status',
        'balance',
        'external_id',
        'owner',
        'hash',
    ];

    protected $casts = [
        'status' => CardStatusEnum::class,
        'number' => 'encrypted',
    ];

    protected $hidden = [
        'hash',
    ];

    public static function hashing(string $value, int $prime = 1000000007, int $base = 37): int
    {
        $hashValue = 0;
        for ($i = 0; $i < strlen($value); $i++) {
            $hashValue = ($hashValue * $base + ord($value[$i])) % $prime;
        }

        return $hashValue;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (self $card) {
            $scoringRequests = ScoringRequest::query()->whereNull('card_id')->when(function ($query) use ($card) {
                $query->where('request->number', $card->number);
            })->get();
            foreach ($scoringRequests as $scoringRequest) {
                $scoringRequest->card_id = $card->id;
                $scoringRequest->customer_id = $card->customer_id;
                $scoringRequest->save();
            }
        });
    }

    public function setPan(): void
    {
        $this->pan = substr_replace($this->number, '******', 6, 6);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function computingBeforeInsert(): void
    {
        $this->status = CardStatusEnum::INACTIVE;
        $this->hash = (int) $this->number;
        $this->setPan();
    }

    public function cardType(): Attribute
    {
        return Attribute::make(get: fn () => CardHelper::getNameByCardNumber($this->number));
    }

    public function hash(): Attribute
    {
        return Attribute::make(set: fn ($value) => self::hashing($value));
    }

    public function isConnected(): Attribute
    {
        return Attribute::make(get: fn () => $this->external_id !== null);
    }

    public function isBindedWithInstallment(): bool
    {
        return $this->installments()->count('id') > 0;
    }

    public function installments(): BelongsToMany
    {
        return $this->belongsToMany(Installment::class, 'installment_cards', 'card_id', 'installment_id')->select('installments.*');
    }

    public function fillData(AtmosCardDTO $data, CardStatusEnum $status)
    {
        $this->external_id = $data->card_token;
        $this->owner = $data->card_holder;
        $this->phone = '+' . $data->phone;
        $this->status = $status;
        $this->saveOrFail();
    }

    public function scopeWhereNumber($query, $cardNumber)
    {
        return $query->where('hash', self::hashing($cardNumber));
    }
}
