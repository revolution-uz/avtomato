<?php

namespace App\Models\Company;

use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory;
    use LogsActivityDirtyOnlyTrait;
    use TenantConnection;

    protected $fillable = [
        'phone',
    ];
}
