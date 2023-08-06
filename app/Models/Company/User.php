<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'status',
        'first_name',
        'last_name',
        'phone'
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
}
