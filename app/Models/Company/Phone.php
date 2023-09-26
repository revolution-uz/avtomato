<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $phone
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 */
class Phone extends Model
{
    protected $dateFormat = 'U';

    protected $fillable = [
        'phone',
    ];
}
