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
 * @method static Builder|CardConfirmationSms query()
 * @method static Builder|CardConfirmationSms whereCode($value)
 */
class CardConfirmationSms extends Model
{
    use LogsActivity;
    use TenantConnection;

    protected $table = 'card_confirmation_sms';

    protected $fillable = [
        'phone',
        'code',
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
