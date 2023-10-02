<?php

namespace App\Nova\Company;

use App\Models\Company\Rbac\RolePermission;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

/**
 * @mixin RolePermission
 */
class RolePermissionResource extends Resource
{
    public static $model = RolePermission::class;

    public static $title = 'id';

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

            Text::make('Permission', function () {
                return $this->permission->slug;
            })
                ->sortable(),

            Text::make('Branch', function () {
                return $this->branch?->name;
            })
                ->sortable(),
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
