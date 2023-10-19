<?php

namespace App\Models\Company;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Card extends Model
{
    use HasFactory;
    use SoftDeletes;
    use TenantConnection;

    protected $fillable = [
        'customer_id',
        'pan',
        'number',
        'expiry',
        'status',
        'balance',
        'myuzkard_id',
    ];

    public function setPan()
    {
        $this->pan = substr_replace($this->number, '******', 6, 6);
        $this->number = Crypt::encryptString($this->number);
    }
}
