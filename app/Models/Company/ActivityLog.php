<?php

namespace App\Models\Company;

use App\Enums\Company\LogType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $log_name
 * @property string $description
 * @property string $subject_type
 * @property int $subject_id
 * @property string $causer_type
 * @property int $causer_id
 * @property array $properties
 * @property string $batch_uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $canceled_at
 * @property LogType $event
 */
class ActivityLog extends Activity
{
    use OrderBySortable, TenantConnection, HasFactory;

    public static array $sortables = ['id', 'admin_id', 'description'];

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'event',
        'causer_type',
        'causer_id',
        'properties',
        'batch_uuid',
    ];

    protected $casts = [
        'properties' => 'collection',
        'event' => LogType::class,
    ];
}
