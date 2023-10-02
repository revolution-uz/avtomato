<?php

namespace App\Models\Company;

use App\DTOs\UserTemplateThemeDto;
use App\Enums\Company\UserStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'username',
        'status',
        'avatar',
        'template_theme',
        'language',
    ];

    protected $casts = [
        'status' => UserStatusEnum::class,
        'template_theme' => UserTemplateThemeDto::class,
    ];

    public function tokens(): MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function createTokenWithDeviceName(string $device_name, string $ip): string
    {
        $token = $this->createToken($device_name);
        $token->accessToken->update(['device_ip' => $ip, 'expires_at' => now()->addDays(30)->format('Y-m-d H:i:s')]);

        return $token->plainTextToken;
    }

    public function avatars(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function phones(): MorphToMany
    {
        return $this->morphToMany(Phone::class, 'phoneable');
    }

    public function avatarPreviewPath(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->avatar) {
                    return $this->avatars()->latest()->first()?->preview_path;
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
