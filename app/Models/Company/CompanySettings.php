<?php

namespace App\Models\Company;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Main\SettingsTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property CompanySettingsEnum $name
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
        'name' => CompanySettingsEnum::class,
    ];

    public static function getConfig(CompanySettingsEnum $settings): ?string
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

    public static function getValue(CompanySettingsEnum $name)
    {
        return CompanySettings::query()->where('name', $name)->firstOrFail()->value;
    }

    protected static function boot()
    {
        parent::boot();

        self::updating(function (self $model) {
            if ($model->isDirty('value') && $model->type === SettingsTypeEnum::FILE) {
                $originalValue = $model->getOriginal('value');
                if ($model->getAttributeValue('value') === null) {
                    $model->setAttribute('value', '');
                } elseif ($originalValue && Storage::disk('local')->exists($originalValue)) {
                    Storage::disk('local')->delete($originalValue);
                }
            }
        });
    }
}
