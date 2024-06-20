<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $oilakredit_id
 * @property string $name
 * @property string $name_uz
 * @property string $name_oz
 * @property string $name_ru
 * @property string $name_en
 */
class Region extends Model
{
    use HasFactory, TenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'oilakredit_id',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
    ];

    public function fullName(): Attribute
    {
        return Attribute::make(get: fn () => $this->{'name_' . app()->getLocale()});
    }
}
