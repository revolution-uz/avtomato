<?php

namespace App\Nova\Company;

use App\Models\Company\Tariff;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class TariffResource extends Resource
{
    public static $model = Tariff::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')->sortable()->rules('required'),
            Number::make('percent')->displayUsing(fn ($value) => $value . '%')->sortable()->rules('required'),
            Number::make('min_period')->sortable()->rules('required'),
            Number::make('max_period')->sortable()->rules('required'),
            Currency::make('min_amount')->currency('UZS'),
            Currency::make('max_amount')->currency('UZS'),
            Currency::make('min_initial_fee')->currency('UZS'),
            Currency::make('max_initial_fee')->currency('UZS'),
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
