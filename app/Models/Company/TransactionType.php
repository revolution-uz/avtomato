<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $name
 * @property string $name_uz
 * @property string $name_ru
 * @property string $name_en
 * @property boolean $is_system
 * @property int $created_at
 * @property int $updated_at
 */
class TransactionType extends Model
{
    use HasFactory;
    use TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'is_system',
        'created_at',
        'updated_at',
    ];

}
