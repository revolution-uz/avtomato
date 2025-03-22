<?php

namespace App\Models\Company\LoyaltyProgram;

use App\DTOs\Atmos\Card\AtmosCardDTO;
use App\Enums\Company\CardStatusEnum;
use App\Helpers\CardHelper;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $pan
 * @property string $number
 * @property int $hash
 * @property string $expiry
 * @property string $owner
 * @property string $phone
 * @property CardStatusEnum $status
 * @property float $balance
 * @property string $external_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $card_type
 * @property-read bool $is_connected
 * @property User $user
 *
 * @method self whereNumber(int $cardNumber)
 */
class Card extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, SoftDeletes, TenantConnection;

    protected $table = 'loyalty_program_cards';

    protected $fillable = [
        'pan',
        'number',
        'hash',
        'expiry',
        'owner',
        'phone',
        'status',
        'balance',
        'external_id',
        'user_id',
    ];

    protected $casts = [
        'status' => CardStatusEnum::class,
        'number' => 'encrypted',
    ];

    protected $hidden = [
        'hash',
        'external_id',
    ];

    public static function hashing(string $value, int $prime = 1000000007, int $base = 37): int
    {
        $hashValue = 0;
        for ($i = 0; $i < strlen($value); $i++) {
            $hashValue = ($hashValue * $base + ord($value[$i])) % $prime;
        }

        return $hashValue;
    }

    public function setPan(): void
    {
        $this->pan = substr_replace($this->number, '******', 6, 6);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function restoreCard()
    {
        $this->update([
            'deleted_at' => null,
            'status' => CardStatusEnum::INACTIVE,
        ]);
    }
}
