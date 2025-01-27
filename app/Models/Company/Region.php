<?php

namespace App\Models\Company;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $oilakredit_id
 * @property string $name_uz
 * @property string $name_oz
 * @property string $name_ru
 * @property string $name_en
 * @property-read string $name
 * @property-read District[] $districts
 */
class Region extends Model
{
    use HasFactory, HasTranslations, TenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'oilakredit_id',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
    ];

    protected $translatedColumns = ['name'];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }
}
