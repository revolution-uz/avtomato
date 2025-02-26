<?php

namespace App\Nova;

use App\Nova\Actions\SbpSyncAccountsAction;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SbpAccountResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SbpAccount>
     */
    public static $model = \App\Models\SbpAccount::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'account_number';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'company_id', 'terminal_id', 'account_number', 'mfo', 'inn', 'purpose_code', 'commission', 'company_name', 'status',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Company', 'company', CompanyResource::class)
                ->sortable()
                ->rules('required', 'max:16'),

            Text::make('Terminal ID')
                ->exceptOnForms()
                ->sortable()
                ->rules('required', 'max:16'),

            Text::make('Account Number')
                ->sortable()
                ->rules('required', 'max:20'),

            Text::make('Mfo')
                ->sortable()
                ->rules('nullable', 'max:5'),

            Text::make('Inn')
                ->sortable()
                ->rules('nullable', 'max:9'),

            Text::make('Purpose Code')
                ->sortable()
                ->rules('nullable', 'max:9'),

            Number::make('Commission')
                ->sortable()
                ->rules('nullable', 'max:9'),

            Text::make('Company Name')
                ->sortable()
                ->rules('nullable', 'max:255'),

            Text::make('Status')
                ->exceptOnForms()
                ->sortable()
                ->rules('nullable', 'max:255'),

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
            (new SbpSyncAccountsAction)->standalone(),
        ];
    }
}
