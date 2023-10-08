<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $district_id
 * @property int $oilakredit_id
 * @property string $name_uz
 * @property string $name_oz
 * @property string $name_ru
 * @property string $name_en
 * @property District $district
 */
class Mahalla extends Model
{
    use HasFactory, TenantConnection;
    public $timestamps = false;

    protected $fillable = [
        'district_id',
        'oilakredit_id',
        'name_uz',
        'name_oz',
        'name_ru',
        'name_en',
    ];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
