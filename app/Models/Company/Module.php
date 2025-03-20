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

    public static function getActiveMultiBranchModule(): ?self
    {
        return self::query()
            ->whereIn('name', ModuleEnum::getMultiBranchModules())
            ->where('is_active', true)
            ->orderBy('name', 'desc')
            ->first();
    }

    protected static function boot(): void
    {
        parent::boot();

        self::updating(function (Module $model) {
            if ($model->isDirty('is_active')) {
                if ($model->is_active) {
                    Feature::query()->whereIn('name', $model->name->getFeatures())->update(['is_active' => true]);
                } else {
                    Feature::query()
                        ->whereIn('name', $model->name->getFeatures())
                        ->whereHas('modules', fn ($q) => $q->where('is_active', true), '=', 1)
                        ->update(['is_active' => false]);
                }
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
