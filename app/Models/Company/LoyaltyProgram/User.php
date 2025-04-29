<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Models\Company\OrderBySortable;
use App\Models\Company\PersonalAccessToken;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read array $qr
 * @property-read int|null $active_subscription_limit
 * @property Subscription[] $subscriptions
 * @property Card[]|Collection<int, Card> $cards
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
        $qrId = (int) $qrDto[0];
        $timestamp = (int) $qrDto[1];
        $userId = $qrId ^ $timestamp ^ tenant('id');

        return static::query()->findOrFail($userId);
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

    public function getQrAttribute(): array
    {
        $timestamp = now()->addSeconds(30)->timestamp;
        $qrId = $this->id ^ $timestamp ^ tenant('id');

        return [
            'id' => $qrId . '$$' . $timestamp,
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

        return $subscription->tariff->limit_amount - $this->getOrderDiscountAmount($subscription->created_at);
    }

    public function getFullNameAttribute(): ?string
    {
        if (! $this->first_name && ! $this->last_name) {
            return null;
        }

        return $this->last_name . ' ' . $this->first_name;
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
