<?php

namespace App\Nova\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Shop;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class ShopResource extends Resource
{
    public static $model = Shop::class;

    public static $title = 'name';

    public static $search = [
        'id', 'external_shop_id', 'name', 'address', 'external_code', 'external_name', 'external_address',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Number::make('External Shop ID', 'external_shop_id')->sortable()->rules('required', 'integer'),
            Text::make('Name', 'name')->sortable()->rules('nullable', 'max:255'),
            Text::make('Address', 'address')->sortable()->rules('nullable', 'max:255'),
            Number::make('Latitude', 'lat')->sortable()->rules('nullable', 'numeric'),
            Number::make('Longitude', 'lng')->sortable()->rules('nullable', 'numeric'),
            Image::make('Image', 'image')->rules('nullable', 'max:255'),
            Select::make('Status', 'status')->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ])->sortable()->rules('required'),
            Text::make('External Code', 'external_code')->sortable()->rules('required', 'max:255'),
            Text::make('External Name', 'external_name')->sortable()->rules('required', 'max:255'),
            Text::make('External Address', 'external_address')->sortable()->rules('required', 'max:255'),
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
