<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

/**
 * @property int $id
 * @property string|null $pinfl
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property Carbon|null $atmos_pinfl_searched_at
 * @property Carbon|null $unired_humo_searched_at
 * @property Carbon|null $unired_uzcard_searched_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class CentralCustomer extends Model
{
    use CentralConnection, HasFactory, SoftDeletes;

    public static array $sortables = ['id', 'pinfl', 'first_name', 'last_name', 'middle_name'];

    protected $table = 'customers';

    protected $fillable = [
        'pinfl',
        'first_name',
        'last_name',
        'middle_name',
        'atmos_pinfl_searched_at',
        'unired_humo_searched_at',
        'unired_uzcard_searched_at',
    ];

    protected $casts = [
        'atmos_pinfl_searched_at' => 'datetime',
        'unired_humo_searched_at' => 'datetime',
        'unired_uzcard_searched_at' => 'datetime',
    ];
}
