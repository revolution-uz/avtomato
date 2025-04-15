<?php

namespace App\Models;

use App\Enums\Main\SettingsEnum;
use App\Enums\Main\SettingsTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property SettingsEnum $name
 * @property string $value
 * @property SettingsTypeEnum|null $type
 * @property string $description
 */
class Settings extends Model
{
    use CentralConnection;

    public static array $_cache = [];

    protected $fillable = [
        'name',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'type' => SettingsTypeEnum::class,
        'name' => SettingsEnum::class,
    ];

    public static function getConfig(SettingsEnum $settings): ?string
    {
        if (! self::$_cache) {
            self::$_cache = self::query()
                ->get()
                ->reduce(function (array $result, self $value, $key) {
                    $result[$value->name->value] = $value->value;

                    return $result;
                }, []);
        }

        return self::$_cache[$settings->value] ?? null;
    }

    public static function getValue(SettingsEnum $name)
    {
        return self::query()->where('name', $name)->firstOrFail()->value;
    }
}
