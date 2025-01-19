<?php

namespace App\Nova\Company;

use App\Enums\Company\CustomerTypeEnum;
use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class CustomerResource extends Resource
{
    public static $model = Customer::class;

    public static $title = 'first_name';

    public static $search = [
        'pinfl',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('first_name')->rules('required'),
            Text::make('last_name')->rules('required'),
            Text::make('middle_name')->rules('required'),
            Text::make('pinfl'),
            Text::make('tin'),
            Text::make('passport_series'),
            Text::make('passport_number'),
            Date::make('passport_given_date'),
            Date::make('passport_expiration_date'),
            Text::make('passport_given_place'),
            Text::make('passport_citizenship'),
            Date::make('birth_date'),
            Text::make('birth_place'),
            Select::make('gender')->options(GenderEnum::options())->displayUsingLabels(),
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
