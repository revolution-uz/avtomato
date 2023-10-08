<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $phone
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 * @property Phoneable|null $pivot
 */
class Phone extends Model
{
    use TenantConnection;

    protected $fillable = [
        'phone',
    ];
}
