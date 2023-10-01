<?php

namespace App\Models\Company;

use App\DTOs\UserTemplateThemeDto;
use App\Enums\Company\UserStatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
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
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, TenantConnection;

    protected $dateFormat = 'U';

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'username',
        'status',
        'avatar',
        'template_theme',
        'language'
    ];

    protected $casts = [
        'status' => UserStatusEnum::class,
        'template_theme' => UserTemplateThemeDto::class
    ];

    /**
     * @param $permission
     * @param $guardName
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        if ($this->getWildcardClass()) {
            return $this->hasWildcardPermission($permission, $guardName);
        }

        $permission = $this->filterPermission($permission, $guardName);

        $hasPermission = $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
        if (($branch_id = session()->get('current_branch')) !== null) {
            return $permission->branch_id === $branch_id && $hasPermission;
        }

        return $hasPermission;
    }

    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    public function createTokenWithDeviceName(string $device_name, string $ip)
    {
        $token = $this->createToken($device_name);
        $token->accessToken->update(['device_ip' => $ip, 'expires_at' => now()->addDays(30)->format("Y-m-d H:i:s")]);
        return $token->plainTextToken;
    }

    public function avatars()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    public function phones()
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

    public function attachPhone(string $phone)
    {
        $model = Phone::firstOrCreate(['phone' => $phone]);
        $this->phones()->sync([$model->id], false);
    }
}
