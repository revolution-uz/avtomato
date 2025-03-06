<?php

namespace App\Nova\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Item;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class ItemResource extends Resource
{
    public static $model = Item::class;

    public static $title = 'external_code';

    public static $search = [
        'id', 'external_code', 'external_shop_id', 'external_art', 'external_name', 'external_mxik', 'external_package_code', 'external_gtin',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('External Code', 'external_code')->sortable()->rules('required', 'max:255'),
            Number::make('External Shop ID', 'external_shop_id')->sortable()->rules('nullable', 'integer'),
            Text::make('External Art', 'external_art')->sortable()->rules('nullable', 'max:255'),
            Text::make('External Name', 'external_name')->sortable()->rules('nullable', 'max:255'),
            Text::make('External MXIK', 'external_mxik')->sortable()->rules('nullable', 'max:255'),
            Text::make('External Package Code', 'external_package_code')->sortable()->rules('nullable', 'max:255'),
            Text::make('External GTIN', 'external_gtin')->sortable()->rules('nullable', 'max:255'),
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

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
}
