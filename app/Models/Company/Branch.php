<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property string $name
 * @property string $inn
 * @property string $address
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 */
class Branch extends Model
{
    use HasFactory, SoftDeletes, TenantConnection;

    protected $fillable = [
        'name',
        'inn',
        'address',
    ];
}
