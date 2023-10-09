<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property int $customer_id
 * @property double $amount
 * @property double $ball
 * @property string $description
 */
class MibScoring extends Model
{
    use HasFactory;
    use TenantConnection;

    protected $fillable = [
        'customer_id',
        'amount',
        'ball',
        'description',
    ];

}
