<?php

namespace App\Models\Company;

use App\Enums\Company\CardStatusEnum;
use App\Helpers\CardHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
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
 *
 * @method static Builder|static query()
 * @method static self find(int $id)
 * @method static self findOrFail(int $id)
 * @method static self first()
 * @method static self firstOrFail()
 * @method self saveOrFail(array $array = [])
 */
class Card extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use TenantConnection;

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
        'number_hash',
    ];

    protected $casts = [
        'status' => CardStatusEnum::class,
        'number' => 'encrypted',
    ];

    protected $hidden = [
        'number_hash',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function setPan(): void
    {
        $this->pan = substr_replace($this->number, '******', 6, 6);
    }
    //
    //    public function setNumber(): void
    //    {
    //        $this->number = Crypt::encryptString($this->number);
    //    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function computingBeforeInsert(): void
    {
        $this->status = CardStatusEnum::INACTIVE;
        $this->setPan();
        //        $this->setNumber();
    }

    public function cardType(): Attribute
    {
        return Attribute::make(get: fn () => CardHelper::getNameByCardNumber($this->number));
    }

    public function isBindedWithInstallment(): bool
    {
        return $this->installments()->count('id') > 0;
    }

    public function installments(): BelongsToMany
    {
        return $this->belongsToMany(Installment::class, 'installment_cards', 'card_id', 'installment_id')->select('installments.*');
    }
}
