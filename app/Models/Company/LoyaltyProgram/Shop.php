<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\ShopStatusEnum;
use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Shop
 *
 * @property int $id
 * @property int $external_shop_id
 * @property string|null $name
 * @property string|null $address
 * @property float|null $lat
 * @property float|null $lng
 * @property string|null $image
 * @property ShopStatusEnum $status
 * @property string $external_code
 * @property string $external_name
 * @property string $external_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class Shop extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    protected static array $sortables = ['id', 'name', 'address', 'external_code', 'external_name', 'external_address', 'status'];

    protected $table = 'loyalty_program_shops';

    protected $fillable = [
        'name',
        'address',
        'lat',
        'lng',
        'image',
        'status',
        'external_shop_id',
        'external_code',
        'external_name',
        'external_address',
    ];

    protected $casts = [
        'status' => ShopStatusEnum::class,
    ];
}
