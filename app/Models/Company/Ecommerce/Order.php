<?php

namespace App\Models\Company\Ecommerce;

use App\Models\Company\Address;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 * @property int $status_id
 * @property int|null $installment_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $customer
 * @property-read Address $address
 * @property-read OrderStatus $status
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'e_commerce_orders';
    protected $fillable = [
        'user_id',
        'address_id',
        'status_id',
        'installment_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }
}
