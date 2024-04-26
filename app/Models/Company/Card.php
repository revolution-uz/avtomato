<?php

namespace App\Models\Company;

use App\Enums\ClientCodeEnum;
use App\Enums\Company\CardStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
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
 * @property ClientCodeEnum $client_code
 * @property float $balance
 * @property string $external_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
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
        'client_code',
        'owner',
    ];

    protected $casts = [
        'status' => CardStatusEnum::class,
        'client_code' => ClientCodeEnum::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

    public function setPan()
    {
        $this->pan = substr_replace($this->number, '******', 6, 6);
        $this->number = Crypt::encryptString($this->number);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
