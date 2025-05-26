<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * Class Item
 *
 * @property int $id
 * @property string $external_code
 * @property string|null $external_shop_code
 * @property string|null $external_art
 * @property string|null $external_name
 * @property string|null $external_mxik
 * @property string|null $external_package_code
 * @property string|null $external_gtin
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Shop|null $shop
 * @property Price[]|Collection<int, Price> $prices
 * @property Stock[]|Collection<int, Stock> $stocks
 * @property-read float|null $price
 * @property-read float|null $quantity
 */
class Item extends Model
{
    use HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    protected static array $sortables = ['id', 'external_code', 'external_shop_code', 'external_art', 'external_name', 'external_mxik', 'external_package_code', 'external_gtin'];

    protected $table = 'loyalty_program_items';

    protected $fillable = [
        'external_code',
        'external_shop_code',
        'external_art',
        'external_name',
        'external_mxik',
        'external_package_code',
        'external_gtin',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'external_shop_code', 'external_code')->withTrashed();
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'external_item_code', 'external_code');
        // ->where('external_shop_code', $this->external_shop_code);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'external_item_code', 'external_code');
        // ->where('external_shop_code', $this->external_shop_code);
    }

    public function getPriceAttribute()
    {
        return $this->prices()->latest()->first()?->external_price;
    }

    public function getQuantityAttribute()
    {
        return $this->stocks()->latest()->first()?->external_quantity;
    }
}
