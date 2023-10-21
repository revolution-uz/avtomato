<?php

namespace App\Models\Company\Rbac;

use App\Models\Company\Feature;
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
        'permission_group_id',
        'feature_id',
        'guard_name',
    ];

    public function permissionGroup(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => __('permissions.' . $this->slug)
        );
    }
}
