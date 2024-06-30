<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $feature_id
 * @property int $amount
 * @property int $chargeable_id
 * @property string $chargeable_type
 * @property Carbon $created_at
 * @property-read Model $chargeable
 * @property Feature $feature
 */
class BalanceCharge extends Model
{
    use LogsActivityDirtyOnlyTrait;
    use OrderBySortable;
    use TenantConnection;

    public static array $sortables = ['id', 'feature_id', 'amount', 'created_at'];

    const null UPDATED_AT = null;

    protected $fillable = [
        'feature_id',
        'amount',
        'chargeable_id',
        'chargeable_type',
    ];

    public function chargeable(): MorphTo
    {
        return $this->morphTo('chargeable');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
