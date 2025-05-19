<?php

namespace App\Nova\Company;

use App\Enums\Company\UserStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class CompanyAdminResource extends Resource
{
    public static $model = CompanyAdmin::class;

    public static $title = 'username';

    public static $search = [
        'name',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('first_name')->rules('required'),
            Text::make('last_name')->rules('required'),
            Text::make('username')->rules('required'),
            Select::make('status')->options(UserStatusEnum::options())->displayUsingLabels()->rules('required'),
            Text::make('language')->rules('required'),
            BelongsTo::make('Role', 'role', RoleResource::class),
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
