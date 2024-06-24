<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $region_id
 * @property int $oilakredit_id
 * @property string $name_uz
 * @property string $name_oz
 * @property string $name_ru
 * @property string $name_en
 * @property-read Region $region
 * @property-read string $name
 * @property-read Mahalla[] $mahallas
 */
class District extends Model
{
    use HasFactory, TenantConnection;

    public $timestamps = false;

    protected $fillable = [
        'region_id',
        'oilakredit_id',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function mahallas(): HasMany
    {
        return $this->hasMany(Mahalla::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(get: fn () => $this->{'name_' . app()->getLocale()});
    }
}
