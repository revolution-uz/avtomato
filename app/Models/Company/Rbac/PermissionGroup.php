<?php

namespace App\Models\Company\Rbac;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read string $name
 * @property-read Permission[] $permissions
 */
class PermissionGroup extends Model
{
    protected $fillable = [
        'slug',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
    ];

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->{'name_' . app()->getLocale()}
        );
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
