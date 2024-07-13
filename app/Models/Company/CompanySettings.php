<?php

namespace App\Models\Company;

use App\Enums\Main\SettingsEnum;
use App\Enums\Main\SettingsTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property SettingsEnum $name
 * @property string $value
 * @property SettingsTypeEnum|null $type
 * @property string $description
 */
class CompanySettings extends Model
{
    use TenantConnection;

    public static array $_cache = [];

    protected $table = 'settings';

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
}
