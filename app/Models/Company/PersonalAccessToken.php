<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\PersonalAccessToken
 *
 * @property int $id
 * @property string $name
 * @property string $token
 * @property array $abilities
 * @property Carbon|null $created_at
 * @property Carbon|null $last_used_at
 * @property string|null $device_ip
 * @property string|null $device_type
 * @property string|null $device_os
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

    public function revokeAccessToken()
    {
        $newTokenString = Str::random(64);
        $newHashedToken = Hash::make($newTokenString);
        $this->forceFill(['token' => $newHashedToken])->save();

        return $newTokenString;
    }
}
