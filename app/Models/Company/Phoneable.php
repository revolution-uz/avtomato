<?php

namespace App\Models\Company;

use App\Enums\Company\PhoneTypeEnum;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property PhoneTypeEnum $type
 * @property string $comment
 * @property int $phone_id
 * @property int $phoneable_id
 * @property string $phoneable_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Phone $phone
 */
class Phoneable extends Model
{
    use LogsActivityDirtyOnlyTrait, TenantConnection;

    protected $fillable = [
        'type',
        'comment',
        'phone_id',
        'phoneable_id',
        'phoneable_type',
    ];

    protected $casts = [
        'type' => PhoneTypeEnum::class,
    ];

    public function phone(): BelongsTo
    {
        return $this->belongsTo(Phone::class);
    }
}
