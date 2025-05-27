<?php

namespace App\Nova\Company;

use App\Models\Company\BalanceCharge;
use App\Nova\Actions\FundBalance;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class BalanceChargeResource extends Resource
{
    public static string $model = BalanceCharge::class;

    public static $title = 'id';

    public static $search = [
        'id', 'amount', 'chargeable_id', 'chargeable_type', 'created_at',
    ];

    public static function authorizedToCreate(Request $request): false
    {
        return false;
    }

    public function fields(Request $request): array
    {
        return [
            ID::make()->sortable(),

            Currency::make('Amount')->sortable(),
            BelongsTo::make('Feature', 'feature', FeatureResource::class)->sortable(),
            Text::make('Chargeable Type', 'chargeable_type')->sortable(),
            Number::make('Chargeable ID', 'chargeable_id')->sortable(),
            DateTime::make('Created at', 'created_at')->sortable(),
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
        return [
            (new FundBalance)->standalone(),
        ];
    }

    public function authorizedToDelete(Request $request): false
    {
        return false;
    }

    public function authorizedToUpdate(Request $request): false
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): false
    {
        return false;
    }
}
