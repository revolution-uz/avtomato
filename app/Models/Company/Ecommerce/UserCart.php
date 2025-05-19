<?php

namespace App\Models\Company\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class UserCart
 *
 * @property int $id
 * @property int $quantity
 * @property int $user_id
 * @property int $product_id
 * @property int $variation_id
 * @property User $user
 * @property Product $product
 * @property ProductVariation $variation
 */
class UserCart extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'ecommerce_user_carts';

    protected $fillable = [
        'quantity',
        'user_id',
        'product_id',
        'variation_id',
    ];

    public static function clearCart(int $user_id, int|array $variationIds): int
    {
        return self::where('user_id', $user_id)
            ->whereIn('variation_id', (array) $variationIds)
            ->delete();
    }

    public static function addCart(int $user_id, int $variation_id, int $quantity)
    {
        $model = self::firstOrNew(
            ['user_id' => $user_id, 'variation_id' => $variation_id],
            ['product_id' => ProductVariation::query()->find($variation_id)->product_id]
        );

        $model->quantity = $quantity;
        $model->saveOrFail();

        return $model;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
