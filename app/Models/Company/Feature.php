<?php

namespace App\Models\Company;

use App\Enums\FeatureEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\Feature
 *
 * @property-read int $id
 * @property-read FeatureEnum $name
 * @property-read string|null $description
 * @property bool $is_active
 * @property-read int $original_amount
 * @property int|null $amount
 * @property-read  array|null $original_data
 * @property array|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<Module> $modules
 * @property-read int $chargeable_amount
 */
class Feature extends Model
{
    use HasFactory, TenantConnection;

    private static ?array $features = null;

    protected $fillable = [
        'is_active',
        'amount',
        'data',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name' => FeatureEnum::class,
    ];

    public static function findByName($name): Feature
    {
        /** @var self $feature */
        $feature = self::query()->where('name', $name)->firstOrFail();

        return $feature;
    }

    public static function isFeatureActive(FeatureEnum $feature)
    {
        if (! self::$features) {
            self::$features = self::query()->pluck('is_active', 'name')->toArray();
        }

        return self::$features[$feature->value];
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(
            Module::class,
            'modules_features',
            'feature_id',
            'module_id'
        );
    }

    public function chargeableAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount !== null ? $this->amount : $this->original_amount
        );
    }

    public function description(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name->getDefaultDescription()
        );
    }
}
