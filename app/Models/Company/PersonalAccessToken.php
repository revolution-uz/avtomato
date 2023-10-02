<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\PersonalAccessToken
 *
 * @property int $id
 * @property string $name
 * @property string $token
 * @property array $abilities
 * @property Carbon|null $last_used_at
 * @property string|null $device_ip
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use SoftDeletes, TenantConnection;

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'device_ip',
    ];
}
