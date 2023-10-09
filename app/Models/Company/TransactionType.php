<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property bool $is_system
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class TransactionType extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'is_system',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
