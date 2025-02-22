<?php

namespace App\Models\Company\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserFavourite
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property User $user
 * @property Product $product
 */
class UserFavourite extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'ecommerce_user_favourites';

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public static function removeCart(int $user_id, int $product_id)
    {
        return self::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
