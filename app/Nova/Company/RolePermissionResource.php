<?php

namespace App\Nova\Company;

use App\Models\Company\Rbac\RolePermission;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;

/**
 * @mixin RolePermission
 */
class RolePermissionResource extends Resource
{
    public static $model = RolePermission::class;

    public static $title = 'slug';

    public static $perPageViaRelationship = 20;

    public static $search = [
        'id',
    ];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Role', 'role', RoleResource::class),

            BelongsTo::make('Permission', 'permission', PermissionResource::class),

            BelongsTo::make('Branch', 'branch', BranchResource::class),
        ];
    }

    public function cards(Request $request): array
    {
        return [];
    }

    public function filters(Request $request): array
    {
        return [];
    }

    public function lenses(Request $request): array
    {
        return [];
    }

    public function actions(Request $request): array
    {
        return [];
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
}
