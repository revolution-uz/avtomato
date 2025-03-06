<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Stock
 *
 * @property int $id
 * @property string $external_item_code
 * @property int $external_shop_id
 * @property float $external_quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Stock extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    protected static array $sortables = ['id', 'external_item_code', 'external_shop_id', 'external_quantity'];

    protected $table = 'loyalty_program_stocks';

    protected $fillable = [
        'external_item_code',
        'external_shop_id',
        'external_quantity',
    ];
}
