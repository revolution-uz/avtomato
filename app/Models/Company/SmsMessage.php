<?php

namespace App\Models\Company;

use App\Enums\Company\SmsMessageStatusEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
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
    use LogsActivityDirtyOnlyTrait, TenantConnection;

    protected $fillable = [
        'phone',
        'message',
        'status',
        'ext_id',
    ];

    protected $casts = [
        'status' => SmsMessageStatusEnum::class,
    ];
}
