<?php

namespace App\Models\Company\LoyaltyProgram;

use App\Models\Company\OrderBySortable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 */
class User extends Model
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
}
