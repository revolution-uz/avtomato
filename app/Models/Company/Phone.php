<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $phone
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 *
 * @property Phoneable|null $pivot
 */
class Phone extends Model
{
    use TenantConnection;
    protected $dateFormat = 'U';

    protected $fillable = [
        'phone',
    ];
}
