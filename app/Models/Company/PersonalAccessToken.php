<?php
namespace App\Models\Company;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'device_name',
        'device_ip',
    ];
}