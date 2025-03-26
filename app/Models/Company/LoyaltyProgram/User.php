<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $phone
 * @property string|null $first_name
 * @property string|null $last_name
 * @property Carbon|null $birth_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read array $qr
 * @property-read int|null $active_subscription_limit
 * @property Subscription[] $subscriptions
 * @property Card[]|Collection<int, Card> $cards
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['phone', 'first_name', 'last_name', 'birth_date'];

    public $table = 'loyalty_program_users';

    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'birth_date',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public static function findByQr($qr): ?self
    {
        $qrDto = explode('$$', $qr);
        if (count($qrDto) !== 2 || $qrDto[1] < now()->timestamp) {
            throw new Exception('Invalid QR code');
        }

        return static::query()->findOrFail($qrDto[0]);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function createTokenWithData(string $deviceName, array $data = []): string
    {
        $this->tokens()->delete();
        $token = $this->createToken($deviceName);
        $token->accessToken->update([...$data, 'expires_at' => now()->addDays(30)->format('Y-m-d H:i:s')]);

        return $token->plainTextToken;
    }

    public function getSuccessLoginAttempts(string $deviceName, int $last_time): int
    {
        return $this->tokens()
            ->where('created_at', '>', now()->subSeconds($last_time))
            ->where('expires_at', '>', now())
            ->where('name', '!=', $deviceName)
            ->count('id');
    }

    public function getQrAttribute(): array
    {
        return [
            'id' => $this->id . '$$' . now()->addSeconds(30)->timestamp,
            'expires_in' => now()->addSeconds(30)->timestamp,
        ];
    }

    public function getActiveSubscription(): ?Subscription
    {
        return $this->subscriptions()->where('is_active', true)->first();
    }

    public function getActiveSubscriptionLimitAttribute(): ?int
    {
        if (! $subscription = $this->getActiveSubscription()) {
            return null;
        }

        return $subscription->tariff->limit_amount - $this->getOrderDiscountAmount($subscription->created_at);
    }

    private function getOrderDiscountAmount(Carbon $created_at)
    {
        return $this->orders()
            ->where('created_at', '>', $created_at)
            ->where('status', OrderStatusEnum::COMPLETED)
            ->get()
            ->sum('total_discount_amount');
    }
}
