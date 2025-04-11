<?php

namespace App\Nova\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Stock;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

class StockResource extends Resource
{
    public static $model = Stock::class;

    public static $title = 'external_item_code';

    public static $search = [
        'id', 'external_item_code', 'external_shop_code', 'external_quantity',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('External Item Code', 'external_item_code')->sortable()->rules('required', 'max:255'),
            Number::make('External Shop ID', 'external_shop_code')->sortable()->rules('required', 'integer'),
            Number::make('External Quantity', 'external_quantity')->sortable()->rules('required', 'numeric'),
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
