<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $inn
 * @property string $address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 */
class Branch extends Model
{
    use SoftDeletes;
    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'inn',
        'address'
    ];
}
