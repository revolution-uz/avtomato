<?php

namespace App\Models\Company;

use App\Enums\Company\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property UserStatus $status
 * @property string $avatar
 *
 * @property-read string $avatarUrl
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'phone',
        'status',
        'avatar',
    ];

    protected $casts = [
        'status' => UserStatus::class
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
            return  $permission->branch_id === $branch_id && $hasPermission;
        }

        return $hasPermission;
    }

    public function getAvatarUrlAttribute(): string
    {
        return Storage::url("avatar/$this->avatar");
    }
}
