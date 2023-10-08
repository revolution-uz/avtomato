<?php

namespace App\Models\Company;

use App\Enums\Company\FeatureEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class Feature extends Model
{
    use HasFactory, TenantConnection;

    private static ?array $features = null;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public static function findByName($name)
    {
        return self::query()->whereName($name)->first();
    }

    public static function isFeatureActive(FeatureEnum $feature)
    {
        if (! self::$features) {
            self::$features = self::query()->pluck('is_active', 'name')->toArray();
        }

        return self::$features[$feature->value];
    }
}
