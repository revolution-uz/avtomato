<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class RolePermission extends Model
{
    public $timestamps = false;

    public function getTable()
    {
        return config('permission.table_names.role_has_permissions', parent::getTable());
    }

    protected $fillable = [
        'permission_id',
        'role_id',
        'branch_id',
    ];
}
