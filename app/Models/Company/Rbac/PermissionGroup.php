<?php

namespace App\Models\Company\Rbac;

use App\Enums\Company\Rbac\PermissionGroupEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property-read PermissionGroupEnum $slug
 * @property-read string $name
 * @property-read Permission[] $permissions
 */
class PermissionGroup extends Model
{
    protected $fillable = [
        'slug',
    ];

    protected $casts = [
        'slug' => PermissionGroupEnum::class,
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => __('permission.' . $this->slug->value)
        );
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
