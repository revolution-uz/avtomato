<?php

namespace App\Models\Company\Rbac;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'label_uz',
        'label_oz',
        'label_ru',
        'label_en',
    ];

    public function label(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->{"label_" . app()->getLocale()}
        );
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }
}
