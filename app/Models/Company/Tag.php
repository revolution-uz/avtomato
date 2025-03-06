<?php

namespace App\Models\Company;

use App\Traits\HasTranslations;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property string $name_oz
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $name
 * @property Customer[] $customers
 */
class Tag extends Model
{
    use HasFactory, HasTranslations, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'name_uz', 'name_ru', 'name_oz', 'name_en', 'created_at', 'updated_at'];

    protected $fillable = [
        'name_uz',
        'name_ru',
        'name_oz',
        'name_en',
    ];

    protected $translatedColumns = ['name'];

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_tags');
    }
}
