<?php

namespace App\Nova\Company;

use App\Models\Company\BalanceFund;
use App\Nova\Actions\FundBalance;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class BalanceFundResource extends Resource
{
    public static string $model = BalanceFund::class;

    public static $title = 'id';

    public static $search = [
        'id', 'amount', 'external_transaction_id', 'phone_number', 'card_number',
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
            Text::make('Phone number', 'phone_number')->sortable(),
            Number::make('Ext ID', 'external_transaction_id')->sortable(),
            Number::make('Terminal ID', 'terminal_id')->sortable(),
            Number::make('Merchant ID', 'merchant_id')->sortable(),
            Text::make('Card', 'card_number')->sortable(),
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
