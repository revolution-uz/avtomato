<?php

namespace App\Models\Company;

use App\Enums\Company\FeaturesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Features extends Model
{
    use HasFactory;

    private static ?array $features = null;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public static function isFeatureActive(FeaturesEnum $feature) {
        if (!self::$features) {
            self::$features = self::query()->pluck('is_active', 'name')->toArray();
        }

        return self::$features[$feature->value];
    }
}
