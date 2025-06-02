<?php

namespace App\Models\Company;

use App\Enums\FeatureEnum;
use App\Exceptions\GeneralException;
use App\Services\IcanService;
use App\Traits\HasTranslations;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property bool $is_system
 * @property string|null $ican_flag
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Transaction[] $transactions
 */
class TransactionType extends Model
{
    use HasFactory, HasTranslations, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'name', 'is_system', 'created_at', 'updated_at'];

    private static array $_cache = [];

    const int ATMOS_WITH_CARD = 1;
    const int ATMOS_WITH_PINFL = 2;
    const int UNIRED_WITH_PINFL = 3;

    protected $fillable = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'is_system',
        'ican_flag',
    ];

    protected $translatedColumns = ['name'];

    public static function isAutoPay($value): bool
    {
        return in_array($value, self::autoPayList());
    }

    public static function autoPayList(): array
    {
        return [self::ATMOS_WITH_CARD, self::ATMOS_WITH_PINFL, self::UNIRED_WITH_PINFL];
    }

    public static function getFeature(int $transaction_type_id): ?FeatureEnum
    {
        return match ($transaction_type_id) {
            self::ATMOS_WITH_CARD => FeatureEnum::ATMOS_CARD_PAY,
            self::ATMOS_WITH_PINFL => FeatureEnum::ATMOS_PINFL_PAY,
            self::UNIRED_WITH_PINFL => FeatureEnum::UNIRED_PINFL_PAY,
            default => null,
        };
    }

    public static function getIdByIcanFlag(string $flag): int
    {
        if (empty(self::$_cache)) {
            self::$_cache = self::query()->whereNotNull('ican_flag')->pluck('id', 'ican_flag')->toArray();
        }

        if (! array_key_exists($flag, self::$_cache)) {
            $icanService = app(IcanService::class);
            $icanService->syncTransactionTypes();
            self::$_cache = self::query()->whereNotNull('ican_flag')->pluck('id', 'ican_flag')->toArray();

            if (! array_key_exists($flag, self::$_cache)) {
                throw new GeneralException('Transaction type not found with ican flag: ' . $flag);
            }
        }

        return self::$_cache[$flag];
    }

    public function getIsLocalAttribute(): bool
    {
        return ! $this->ican_flag; // Assuming that if ican_flag is null, it's a local transaction type
    }

    public function setNames(string $name): void
    {
        $this->name_uz = $name;
        $this->name_ru = $name;
        $this->name_en = $name;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
