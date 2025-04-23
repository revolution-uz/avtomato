<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\PaymentStatusEnum;
use App\Models\Company\OrderBySortable;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property float $amount
 * @property PaymentStatusEnum $status
 * @property Carbon|null $paid_at
 * @property int $card_id
 * @property string|null $external_id
 * @property int|null $subscription_id
 * @property int $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property Subscription|null $subscription
 */
class LoyaltyProgramPayment extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, TenantConnection;

    public static array $sortables = [
        'amount',
        'card_id',
        'status',
        'paid_at',
        'external_id',
        'user_id',
    ];

    protected $table = 'loyalty_program_payments';

    protected $fillable = [
        'amount',
        'card_id',
        'status',
        'paid_at',
        'external_id',
        'user_id',
        'subscription_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'status' => PaymentStatusEnum::class,
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
