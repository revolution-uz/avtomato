<?php

namespace App\Models;

use App\Enums\Main\SettingsEnum;
use App\Enums\Main\SettingsTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string $name
 * @property string $value
 * @property SettingsTypeEnum|null $type
 * @property string $description
 */
class Settings extends Model
{
    use CentralConnection;
    protected $dateFormat = 'U';

    public static array $_cache = [];
    protected $fillable = [
        'name',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'type' => SettingsTypeEnum::class,
    ];

    public static function getConfig(SettingsEnum $settings): ?string
    {
        if (!self::$_cache) {
            self::$_cache = self::query()
                ->get()
                ->pluck('value', 'name')
                ->toArray();
        }

        return self::$_cache[$settings->value] ?? null;
    }
}
