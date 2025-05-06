<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property array|null $abilities
 * @property Carbon|null $last_used_at
 * @property string|null $device_ip
 * @property string|null $device_type
 * @property string|null $device_os
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'name', 'device_ip', 'device_type', 'device_os', 'last_used_at'];

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'device_ip',
        'device_type',
        'device_os',
    ];
}
