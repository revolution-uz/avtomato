<?php

namespace App\Models\Company;

use App\Enums\Company\SmsMessageStatusEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $phone
 * @property int|null $ext_id
 * @property string $message
 * @property SmsMessageStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class SmsMessage extends Model
{
    use LogsActivityDirtyOnlyTrait, TenantConnection, HasFactory;

    protected $fillable = [
        'phone',
        'message',
        'status',
        'ext_id',
    ];

    protected $casts = [
        'status' => SmsMessageStatusEnum::class,
    ];

    public function charge(): MorphOne
    {
        return $this->morphOne(BalanceCharge::class, 'chargeable');
    }
}
