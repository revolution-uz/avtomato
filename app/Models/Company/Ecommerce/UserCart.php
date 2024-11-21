<?php

namespace App\Models\Company\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserCart
 *
 * @property int $user_id ID of the user
 * @property int $product_id ID of the product
 * @property int $quantity Quantity of the product in the cart
 * @property User $user
 * @property Product $product
 */
class UserCart extends Model
{
    use HasFactory;

    // Disable timestamps if they are not used in this table
    public $timestamps = false;

    // Define the table name if it doesn't follow the Laravel naming convention
    protected $table = 'ecommerce_user_carts';

    // Define fillable fields to allow mass assignment
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
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
            $model->quantity = $quantity;
        } else {
            $model = new self;
            $model->user_id = $user_id;
            $model->product_id = $product_id;
            $model->quantity = $quantity;
            $model->saveOrFail();
        }

        return $model;
    }

    /**
     * Define a relationship to the User model (assuming a User model exists)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define a relationship to the Product model (assuming a Product model exists)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
