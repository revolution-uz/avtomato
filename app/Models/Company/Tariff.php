<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name
 * @property float $percent
 * @property int $min_period
 * @property int $max_period
 * @property int $min_amount
 * @property int $max_amount
 * @property int $min_initial_fee
 * @property int $max_initial_fee
 * @property-read Installment[] $installments
 */
class Tariff extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'name', 'percent'];

    protected $fillable = [
        'name',
        'percent',
        'min_period',
        'max_period',
        'min_amount',
        'max_amount',
        'min_initial_fee',
        'max_initial_fee',
    ];

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }
}
