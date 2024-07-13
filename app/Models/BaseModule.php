<?php

namespace App\Models;

use App\Enums\ModuleEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property ModuleEnum $name
 * @property int $original_amount
 * @property bool $is_default_active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, BaseFeature> $features
 */
class BaseModule extends Model
{
    use CentralConnection;

    protected $fillable = [
        'name',
        'original_amount',
        'is_default_active',
    ];

    protected $casts = [
        'name' => ModuleEnum::class,
        'is_default_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        self::updating(function (BaseModule $model) {
            if (! $model->is_default_active) {
                BaseFeature::query()->whereIn('id', $model->features->pluck('id'))->update(['is_default_active' => false]);
            }
        });
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(
            BaseFeature::class,
            'base_modules_features',
            'module_id',
            'feature_id'
        );
    }
}
