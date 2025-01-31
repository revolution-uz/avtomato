<?php

namespace App\Models\Company\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Feature;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property PermissionEnum $slug
 * @property int $permission_group_id
 * @property int $feature_id
 * @property bool $is_branch_related
 * @property-read string $name
 * @property-read PermissionGroup $permissionGroup
 * @property-read Feature|null $feature
 */
class Permission extends Model
{
    protected $fillable = [
        'slug',
        'permission_group_id',
        'feature_id',
        'guard_name',
    ];

    protected $casts = [
        'slug' => PermissionEnum::class,
    ];

    public function permissionGroup(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => __('permission.' . $this->slug->value)
        );
    }
}
