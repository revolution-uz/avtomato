<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class AuthAttempt
 *
 * @property string $phone
 * @property string|null $code
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @method static Builder|AuthAttempt query()
 * @method static Builder|AuthAttempt whereCode($value)
 */
class AuthAttempt extends Model
{
    use LogsActivity;
    use TenantConnection;

    protected $table = 'auth_attempts';

    protected $fillable = [
        'phone',
        'code',
        'type',
        'notification_type',
    ];

    protected $casts = [
        'phone' => 'string',
        'code' => 'string',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }
}
