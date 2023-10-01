<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $region_id
 * @property int $oilakredit_id
 * @property string $name_uz
 * @property string $name_oz
 * @property string $name_ru
 * @property string $name_en
 * @property Region $region
 */
class District extends Model
{
    use TenantConnection;
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
}
