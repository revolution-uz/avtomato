<?php

namespace App\Models\Company;

use App\Enums\Company\TerminalTypeEnum;
use App\Enums\FeatureEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Feature
 *
 * @property int $id
 * @property FeatureEnum $name
 * @property bool $is_active
 * @property int $original_amount
 * @property int|null $amount
 * @property float|null $humo_percent
 * @property float|null $uzcard_percent
 * @property array|null $original_data
 * @property array|null $data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read int $chargeable_amount
 * @property-read string $description
 * @property Module[]|Collection<int, Module> $modules
 * @property BalanceCharge[]|Collection<int, BalanceCharge> $balanceCharges
 */
class Feature extends Model
{
    use HasFactory, TenantConnection;

    protected $fillable = [
        'is_active',
        'amount',
        'data',
        'humo_percent',
        'uzcard_percent',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name' => FeatureEnum::class,
        'data' => 'encrypted:json',
        'original_data' => 'encrypted:json',
    ];

    public static function findByName($name): Feature
    {
        /** @var self $feature */
        $feature = self::query()->where('name', $name)->firstOrFail();

        return $feature;
    }

    public static function isFeatureActive(FeatureEnum $feature): bool
    {
        return self::findByName($feature)->is_active;
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
            // @phpstan-ignore-next-line
            get: fn () => $this->name?->getDefaultDescription()
        );
    }

    public function balanceCharges(): HasMany
    {
        return $this->hasMany(BalanceCharge::class);
    }

    public function getTerminalData(TerminalTypeEnum $typeEnum, int $branchId): ?array
    {
        if ($terminal = Terminal::query()
            ->where('branch_id', $branchId)
            ->where('type', $typeEnum)
            ->first()) {
            return $terminal->data ?? $this->data;
        }

        return $this->data;
    }
}
