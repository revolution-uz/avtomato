<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\Customer;
use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $phone
 * @property int|null $customer_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property Product[]|Collection<int, Product> $cart
 * @property Product[]|Collection<int, Product> $favorite
 * @property Order[]|Collection<int, Order> $orders
 * @property-read  Customer|null $customer
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, OrderBySortable, SoftDeletes;
    protected static array $sortables = ['id', 'phone', 'customer_id', 'created_at', 'updated_at'];

    protected $table = 'ecommerce_users';
    protected $fillable = [
        'phone',
        'customer_id',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function createTokenWithData(string $deviceName, array $data = []): string
    {
        $token = $this->createToken($deviceName);
        $token->accessToken->update([...$data, 'expires_at' => now()->addDays(30)->format('Y-m-d H:i:s')]);

        return $token->plainTextToken;
    }

    public function cart(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ecommerce_user_carts');
    }

    public function favorite(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'ecommerce_user_favourites');
    }
}
