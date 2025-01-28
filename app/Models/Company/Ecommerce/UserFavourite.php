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

    public static function addCart(int $user_id, int $product_id, int $quantity)
    {
        if ($model = self::where('user_id', $user_id)
            ->where('product_id', $product_id)
            ->first()) {
        } else {
            $model = new self;
            $model->user_id = $user_id;
            $model->product_id = $product_id;
            $model->saveOrFail();
        }

        return $model;
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
