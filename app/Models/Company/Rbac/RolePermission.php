<?php

namespace App\Models\Company\Rbac;

use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Permission $permission
 * @property-read Role $role
 * @property-read Branch|null $branch
 */
class RolePermission extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'permission_id',
        'role_id',
        'branch_id',
    ];

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
