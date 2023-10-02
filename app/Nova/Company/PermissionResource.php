<?php

namespace App\Nova\Company;

use App\Models\Company\Rbac\Permission;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class PermissionResource extends Resource
{
    public static $model = Permission::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'guard_name',
    ];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('nullable'),

            Text::make('Slug')
                ->sortable()
                ->rules('required'),

            Number::make('Feature Id')
                ->sortable()
                ->rules('nullable', 'integer'),
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
