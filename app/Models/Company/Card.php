<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $customer_id
 * @property string $pan
 * @property string $number
 * @property string $expiry
 * @property int $status
 * @property double $balance
 * @property string $myuzkard_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 */
class Card extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dateFormat = 'U';

    protected $fillable = [
        'user_id',
        'customer_id',
        'pan',
        'number',
        'expiry',
        'status',
        'balance',
        'myuzkard_id',
    ];
}
