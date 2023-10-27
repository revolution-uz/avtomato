<?php

namespace App\Models\Company;

use App\DTOs\UserTemplateThemeDto;
use App\Enums\Company\UserStatusEnum;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property UserStatusEnum $status
 * @property string $avatar
 * @property UserTemplateThemeDto $template_theme
 * @property string $language
 * @property-read string $avatar_url
 * @property-read string $avatar_preview_url
 * @property-read Role|null $role
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, TenantConnection;

    public const AVATAR_PATH = 'avatars';
    public const THUMBNAIL_PATH = 'avatars/thumbnails';

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'username',
        'status',
        'avatar',
        'template_theme',
        'language',
        'role_id',
        'password',
    ];

    protected $casts = [
        'status' => UserStatusEnum::class,
        'template_theme' => UserTemplateThemeDto::class,
    ];

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

    public function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar) {
                    return asset(User::AVATAR_PATH . '/' . $this->avatar);
                }

                return null;
            }
        );
    }

    public function avatarPreviewUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar) {
                    return asset(User::THUMBNAIL_PATH . '/' . $this->avatar);
                }

                return null;
            }
        );
    }

    public function attachPhone(string $phone): void
    {
        $model = Phone::firstOrCreate(['phone' => $phone]);
        $this->phones()->sync([$model->id], false);
    }
}
