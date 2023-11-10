<?php

namespace App\Models\Company;

use App\Enums\Company\LogType;
use Spatie\Activitylog\Models\Activity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property LogType $event
 */
class ActivityLog extends Activity
{
    use TenantConnection;

    protected $casts = [
        'properties' => 'collection',
        'event' => LogType::class,
    ];
}
