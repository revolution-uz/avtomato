<?php

namespace App\Nova\Company;

use App\Models\Company\Rbac\Permission;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;

class PermissionResource extends Resource
{
    public static $model = Permission::class;

    public static $title = 'slug';

    public static $search = [
        'id', 'slug', 'name',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Slug')
                ->sortable()
                ->rules('required'),

            BelongsTo::make('Feature', 'feature', FeatureResource::class),
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
}
