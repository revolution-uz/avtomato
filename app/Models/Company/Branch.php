<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string $inn
 * @property string $address
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
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
