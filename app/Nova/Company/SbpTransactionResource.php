<?php

namespace App\Nova\Company;

use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Models\Company\SbpTransaction;
use App\Nova\Actions\SbpTransactionAmountAction;
use App\Nova\Actions\SbpTransactionRefreshAction;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SbpTransactionResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<SbpTransaction>
     */
    public static $model = SbpTransaction::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'ext_id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'ext_id', 'amount', 'status',
    ];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Terminal Id')
                ->sortable()
                ->rules('required', 'max:20'),

            Text::make('Ext Id')
                ->sortable(),

            Text::make('Amount')
                ->sortable()
                ->rules('required', 'max:20'),
            Select::make('status')->options(TransactionAttemptStatusEnum::options())->displayUsingLabels()->exceptOnForms(),
            Code::make('Credit')->json(),
            Code::make('Debit')->json(),
            Code::make('Sender')->json(),
            Code::make('Receiver')->json(),
            Number::make('Currency'),
            DateTime::make('Created At'),
            DateTime::make('Payment Updated At'),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new SbpTransactionAmountAction)->standalone(),
            (new SbpTransactionRefreshAction)->onlyInline(),
        ];
    }
}
