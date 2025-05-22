<?php

namespace App\Models\Company;

use App\DTOs\UserTemplateThemeDto;
use App\Enums\Company\UserStatusEnum;
use App\Models\Company\Rbac\Role;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\CausesActivity;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string $phone
 * @property int $role_id
 * @property UserStatusEnum $status
 * @property UserTemplateThemeDto $template_theme
 * @property string $language
 * @property string $password
 * @property-read Attachment|null $avatar
 * @property-read Role|null $role
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class CompanyAdmin extends Authenticatable
{
    use CausesActivity, HasApiTokens, HasFactory, LogsActivityDirtyOnlyTrait, Notifiable, OrderBySortable, SoftDeletes, TenantConnection;

    public static array $sortables = ['id', 'username', 'first_name', 'last_name', 'created_at', 'updated_at'];

    const ICAN_INTEGRATOR = 'ican_integrator';

    protected $table = 'admins';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'status',
        'template_theme',
        'language',
        'role_id',
        'password',
    ];

    protected $casts = [
        'status' => UserStatusEnum::class,
        'template_theme' => UserTemplateThemeDto::class,
    ];

    public static function getIcanIntegrator()
    {
        return self::query()->withTrashed()->where('username', self::ICAN_INTEGRATOR)->firstOrFail();
    }

    public function tokens(): MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function createTokenWithData(string $deviceName, array $data = []): string
    {
        $token = $this->createToken($deviceName);
        $token->accessToken->update([...$data, 'expires_at' => now()->addDays(30)->format('Y-m-d H:i:s')]);

        return $token->plainTextToken;
    }

    public function phones(): MorphToMany
    {
        return $this->morphToMany(Phone::class, 'phoneable');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function avatar(): MorphOne
    {
        return $this->morphOne(Attachment::class, 'attachmentable')->latestOfMany();
    }

    public function attachPhone(string $phone): void
    {
        $model = Phone::firstOrCreate(['phone' => $phone]);
        $this->phones()->sync([$model->id], false);
    }

    public function fullName(): Attribute
    {
        return Attribute::make(get: fn () => $this->last_name . ' ' . $this->first_name);
    }
}
