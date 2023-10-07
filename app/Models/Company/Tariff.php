<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name
 * @property bool $is_annual
 * @property float $percent
 * @property int $min_amount
 * @property int $max_amount
 * @property int $min_initial_fee
 * @property int $max_initial_fee
 */
class Tariff extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'is_annual',
        'percent',
        'min_amount',
        'max_amount',
        'min_initial_fee',
        'max_initial_fee',
    ];
}
