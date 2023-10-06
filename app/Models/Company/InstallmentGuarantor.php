<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $installment_id
 * @property int $customer_id
 * @property boolean $auto_charge
 * @property boolean $any_card_charge
 * @property int $status
 * @property string $myuzkard_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $deleted_at
 */
class InstallmentGuarantor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dateFormat = 'U';

    protected $fillable = [
        'installment_id',
        'customer_id',
        'auto_charge',
        'any_card_charge',
        'status',
        'myuzkard_id',
    ];
}
