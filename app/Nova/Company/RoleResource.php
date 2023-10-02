<?php

namespace App\Nova\Company;

use App\Models\Company\Rbac\Role;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class RoleResource extends Resource
{
    public static $model = Role::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'guard_name'
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required'),

            Text::make('Guard Name')
                ->sortable()
                ->rules('required'),

            HasMany::make('Permissions', 'permissions', PermissionResource::class)
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

    public static function authorizedToCreate(Request $request)
    {
        return false;
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
