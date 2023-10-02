<?php

namespace App\Models\Company\Rbac;

use Illuminate\Database\Eloquent\Casts\Attribute;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable = [
        'name',
        'label_uz',
        'label_oz',
        'label_ru',
        'label_en',
        'permission_group_id',
        'guard_name',
    ];

    public function permissionGroup()
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function label(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->{"label_" . app()->getLocale()}
        );
    }
}
