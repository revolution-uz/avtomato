<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Stock
 *
 * @property int $id
 * @property string $external_item_code
 * @property string $external_shop_code
 * @property float $external_quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Stock extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    protected static array $sortables = ['id', 'external_item_code', 'external_shop_code', 'external_quantity'];

    protected $table = 'loyalty_program_stocks';

    protected $fillable = [
        'external_item_code',
        'external_shop_code',
        'external_quantity',
    ];
}
