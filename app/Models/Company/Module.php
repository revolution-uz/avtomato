<?php

namespace App\Models\Company;

use App\Enums\ModuleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\Module
 *
 * @property-read int $id
 * @property-read ModuleEnum $name
 * @property bool $is_active
 * @property-read int $original_amount
 * @property int|null $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Module extends Model
{
    use TenantConnection;

    protected $fillable = [
        'is_active',
        'amount',
    ];

    protected $casts = [
        'name' => ModuleEnum::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::updating(function (Module $model) {
            if (! $model->is_active) {
                Feature::query()->whereIn('id', $model->features->pluck('id'))->update(['is_active' => false]);
            }
        });
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            Feature::class,
            'modules_features',
            'module_id',
            'feature_id'
        );
    }
}
