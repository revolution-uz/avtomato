<?php

namespace App\Models\Company\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Feature;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property PermissionEnum $slug
 * @property int $permission_group_id
 * @property int $feature_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property PermissionGroup $permissionGroup
 * @property Feature $feature
 * @property-read string $name
 * @property-read bool $is_branch_related
 */
class Permission extends Model
{
    protected $fillable = [
        'slug',
        'permission_group_id',
        'feature_id',
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

    public function getIsBranchRelatedAttribute()
    {
        return PermissionEnum::getIsBranchRelated($this->slug);
    }
}
