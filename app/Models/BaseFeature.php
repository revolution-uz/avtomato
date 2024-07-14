<?php

namespace App\Models;

use App\Enums\FeatureEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property FeatureEnum $name
 * @property int $original_amount
 * @property bool $is_default_active
 * @property array|null $original_data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read string|null $description
 * @property-read Collection<BaseModule> $modules
 */
class BaseFeature extends Model
{
    use CentralConnection;

    protected $fillable = [
        'name',
        'original_amount',
        'is_default_active',
        'original_data',
    ];

    protected $casts = [
        'name' => FeatureEnum::class,
        'is_default_active' => 'boolean',
        'original_data' => 'encrypted:json',
    ];

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            BaseModule::class,
            'base_modules_features',
            'feature_id',
            'module_id'
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name->getDefaultDescription()
        );
    }
}
