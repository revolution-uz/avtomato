<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Item
 *
 * @property int $id
 * @property string $external_code
 * @property int|null $external_shop_id
 * @property string|null $external_art
 * @property string|null $external_name
 * @property string|null $external_mxik
 * @property string|null $external_package_code
 * @property string|null $external_gtin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Item extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    protected static array $sortables = ['id', 'external_code', 'external_shop_id', 'external_art', 'external_name', 'external_mxik', 'external_package_code', 'external_gtin'];

    protected $table = 'loyalty_program_items';

    protected $fillable = [
        'external_code',
        'external_shop_id',
        'external_art',
        'external_name',
        'external_mxik',
        'external_package_code',
        'external_gtin',
    ];
}
