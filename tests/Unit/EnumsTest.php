<?php

namespace Tests\Unit;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\PermissionGroupEnum;
use Illuminate\Support\Arr;
use Tests\TestCase;

class EnumsTest extends TestCase
{
    /**
     * @test
     */
    public function permissionGroupShouldCoverAllPermissions(): void
    {
        $permissions = [];

        foreach (PermissionGroupEnum::cases() as $permissionGroupEnum) {
            $groupPermissions = PermissionGroupEnum::getPermissionsByGroup($permissionGroupEnum);

            if (Arr::first($groupPermissions, fn(PermissionEnum $permission) => in_array($permission, $permissions))) {
                $this->fail($permissionGroupEnum->name . ' has duplicate permissions');
            }

            $permissions = [...$permissions, ...$groupPermissions];
        }

        foreach (PermissionEnum::cases() as $permissionEnum) {
            $this->assertTrue(in_array($permissionEnum, $permissions), $permissionEnum->name . ' is not covered by a permission group');
        }
    }
}
