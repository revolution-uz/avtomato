<?php

namespace App\Nova;

use App\Models\CentralBalanceFund;
use App\Nova\Metrics\CompaniesMetric;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CentralBalanceFundResource extends Resource
{
    public static string $model = CentralBalanceFund::class;

    public static $title = 'id';

    public static $search = [
        'id', 'amount', 'external_transaction_id', 'card_pan',
    ];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Card', 'card_pan')->sortable(),
            Text::make('Amount')->displayUsing(fn () => number_format($this->amount, thousands_separator: ' ') . ' UZS')->sortable(),
            Text::make('Phone number', 'phone_number')->hideFromIndex(),
            Number::make('Ext ID', 'external_transaction_id')->hideFromIndex(),
            Number::make('Terminal ID', 'terminal_id')->hideFromIndex(),
            Number::make('Merchant ID', 'merchant_id')->hideFromIndex(),
            BelongsTo::make('Company', 'company', CompanyResource::class)->filterable(),
            DateTime::make('Created at', 'created_at')->sortable(),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            CompaniesMetric::make()->width('full'),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }
}
