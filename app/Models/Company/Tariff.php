<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property boolean $is_annual
 * @property double $percent
 * @property int $min_amount
 * @property int $max_amount
 * @property int $min_initial_fee
 * @property int $max_initial_fee
 */
class Tariff extends Model
{
    use SoftDeletes;

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
