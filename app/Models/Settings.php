<?php

namespace App\Models;

use App\Database\DatabaseConnector;
use App\Enums\Main\SettingsEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $type
 * @property string $description
 */
class Settings extends Model
{
    protected $connection = DatabaseConnector::DB_CONNECTION_ADMIN;
    public static array $_cache = [];
    protected $fillable = [
        'name',
        'value',
        'type',
        'description',
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
