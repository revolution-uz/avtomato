<?php

namespace App\Console\Commands;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Rbac\RolePermission;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncBranchRelatedPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync-branch-related';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize branch-related permissions for companies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // todo: test for only REVO_TENANT_ID
        /** @var Company[] $companies */
        $companies = Company::query()->where('id', Company::REVO_TENANT_ID)->get();

        foreach ($companies as $company) {
            tenancy()->initialize($company);

            try {
                $roles = Role::all();
                $permissions = Permission::all();

                DB::beginTransaction();

                foreach ($permissions as $permission) {
                    $permission->update(['is_branch_related' => PermissionEnum::getIsBranchRelated($permission->slug)]);

                    if ($permission->is_branch_related) {
                        RolePermission::query()->where('permission_id', $permission->id)
                            ->whereNull('branch_id')
                            ->delete();
                    } else {
                        foreach ($roles as $role) {
                            $rolePermission = RolePermission::query()
                                ->where('permission_id', $permission->id)
                                ->where('role_id', $role->id)
                                ->first();

                            if ($rolePermission) {
                                if ($rolePermission->branch_id) {
                                    $rolePermission->update(['branch_id' => null]);
                                }

                                RolePermission::query()
                                    ->where('permission_id', $permission->id)
                                    ->where('role_id', $role->id)
                                    ->whereNotNull('branch_id')
                                    ->delete();
                            }
                        }
                    }
                }

                DB::commit();
                $this->info('Finished company ' . $company->id);

            } catch (Exception $e) {
                DB::rollBack();
                $this->error('Error with company ' . $company->id . ': ' . $e->getMessage());
            }
        }

        $this->info('Branch-related permissions synchronized successfully');
    }
}
