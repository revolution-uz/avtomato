<?php

namespace App\Nova\Company;

use App\Enums\Company\CustomerTypeEnum;
use App\Models\Company\CompanyAdmin;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class CustomerResource extends Resource
{
    public static $model = CompanyAdmin::class;

    public static $title = 'first_name';

    public static $search = [
        'name',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('first_name')->rules('required'),
            Text::make('last_name')->rules('required'),
            Text::make('middle_name')->rules('required'),
            Select::make('type')->options(CustomerTypeEnum::options())->displayUsingLabels()->rules('required'),
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
