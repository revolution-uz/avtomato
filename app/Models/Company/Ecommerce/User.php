<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\Customer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $phone
 * @property int $customer_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Model
{
    use HasApiTokens, HasFactory, SoftDeletes;
    protected $table = 'e_commerce_users';
    protected $fillable = [
        'phone',
        'customer_id',
    ];

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
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
}
