<?php

namespace App\Models\Company;

use App\Enums\Company\UserStatusEnum;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property UserStatusEnum $status
 * @property string $avatar
 * @property bool $is_dark_mode
 * @property bool $is_vertical_menu
 * @property bool $is_bordered_items
 * @property bool $is_fixed_navbar
 * @property bool $is_fixed_footer
 * @property bool $is_fixed_menu
 * @property bool $is_show_dropdown_on_hover
 * @property string $language
 *
 * @property-read string $avatar_url
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    public const AVATAR_FOLDER = 'avatar';

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'username',
        'phone',
        'status',
        'avatar',
        'is_dark_mode',
        'is_vertical_menu',
        'is_bordered_items',
        'is_fixed_navbar',
        'is_fixed_footer',
        'is_fixed_menu',
        'is_show_dropdown_on_hover',
        'language'
    ];

    protected $casts = [
        'status' => UserStatusEnum::class
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

    public function getAvatarUrlAttribute(): string
    {
        return Storage::url(self::AVATAR_FOLDER . "/$this->avatar");
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
}
