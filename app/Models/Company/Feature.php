<?php

namespace App\Models\Company;

use App\Enums\Company\FeatureEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * App\Models\Company\Feature
 *
 * @property int $id
 * @property FeatureEnum $name
 * @property string|null $description
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Feature extends Model
{
    use HasFactory, TenantConnection;

    private static ?array $features = null;

    protected $fillable = [
        'name',
        'description',
        'is_active',
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
}
