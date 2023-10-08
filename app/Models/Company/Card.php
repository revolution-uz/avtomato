<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $pan
 * @property string $number
 * @property string $expiry
 * @property int $status
 * @property float $balance
 * @property string $myuzkard_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 */
class Card extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'customer_id',
        'pan',
        'number',
        'expiry',
        'status',
        'balance',
        'myuzkard_id',
    ];
}
