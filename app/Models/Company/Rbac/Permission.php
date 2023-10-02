<?php

namespace App\Models\Company\Rbac;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read string $name
 * @property-read PermissionGroup $permissionGroup
 */
class Permission extends Model
{
    protected $fillable = [
        'slug',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
        'permission_group_id',
        'guard_name',
    ];

    public function permissionGroup(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->{'name_' . app()->getLocale()}
        );
    }
}
