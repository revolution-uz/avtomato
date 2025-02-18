<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $pinfl
 * @property string $uuid
 * @property bool|null $auto
 * @property bool|null $is_verified
 * @property bool|null $is_blocked
 * @property string|null $block_reason
 * @property Carbon|null $blocked_at
 * @property string|null $owner
 * @property string|null $pan
 * @property string|null $phone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UniredCard extends Model
{
    use HasFactory, TenantConnection;

    protected $fillable = [
        'customer_id',
        'pinfl',
        'uuid',
        'auto',
        'is_verified',
        'is_blocked',
        'block_reason',
        'blocked_at',
        'owner',
        'pan',
        'phone',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
    ];
}
