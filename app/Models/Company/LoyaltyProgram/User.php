<?php

namespace App\Models\Company\LoyaltyProgram;

use App\DTOs\LoyaltyProgram\QrPayloadDTO;
use App\Models\Company\OrderBySortable;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $phone
 * @property string|null $first_name
 * @property string|null $last_name
 * @property Carbon|null $birth_date
 * @property string|null $main_device
 * @property int|null $shopmate_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read int|null $active_subscription_limit
 * @property Subscription[] $subscriptions
 * @property Card[]|Collection<int, Card> $cards
 * @property self|null $shopmate
 * @property-read string|null $full_name
 * @property-read bool $has_available_subscription
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
        'main_device',
        'shopmate_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

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

    public function shopmate(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    public function tokens(): MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function createTokenWithData(string $deviceName, array $data = []): string
    {
        $this->tokens()->delete();
        $token = $this->createToken($deviceName);
        $token->accessToken->update([...$data, 'expires_at' => now()->addDays(30)->format('Y-m-d H:i:s')]);

        return $token->plainTextToken;
    }

    public function getSuccessLoginAttempts(int $last_time): int
    {
        return $this->tokens()
            ->withTrashed()
            ->where('created_at', '>', now()->subSeconds($last_time))
            ->where('expires_at', '>', now())
            ->whereNot('name', $this->main_device)
            ->count();
    }

    public function getQR(int $expiration = 10, bool $isShopmate = false): ?array
    {
        if (($isShopmate && ! $this->shopmate_id) || (! $isShopmate && ! $this->has_available_subscription)) {
            return null;
        }

        $timestamp = now()->addSeconds($expiration)->timestamp;

        $qrDto = QrPayloadDTO::from([
            'user_id' => $this->id,
            'timestamp' => $timestamp,
            'tenant_id' => tenant('id'),
            'isShopmate' => $isShopmate,
        ]);

        return [
            'id' => $qrDto->generateQr(),
            'expires_in' => $timestamp,
        ];
    }

    public function getHasAvailableSubscriptionAttribute(): bool
    {
        return $this->subscriptions()->whereIsAvailable()->exists();
    }

    public function getAvailableSubscription(): ?Subscription
    {
        return $this->subscriptions()->whereIsAvailable()->first();
    }

    public function getActiveSubscriptionLimitAttribute(): ?int
    {
        if (! $subscription = $this->getAvailableSubscription()) {
            return null;
        }

        return $subscription->remaining_limit_amount;
    }

    public function getFullNameAttribute(): ?string
    {
        if (! $this->first_name && ! $this->last_name) {
            return null;
        }

        return $this->last_name . ' ' . $this->first_name;
    }
}
