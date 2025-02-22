<?php

namespace App\Nova;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CentralCustomerResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\CentralCustomer>
     */
    public static $model = \App\Models\CentralCustomer::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'first_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'pinfl', 'first_name', 'last_name', 'middle_name',
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
            Text::make('pinfl')->sortable()->rules('required', 'max:16'),
            Text::make('first_name')->sortable()->rules('nullable', 'max:255'),
            Text::make('last_name')->sortable()->rules('nullable', 'max:255'),
            Text::make('middle_name')->sortable()->rules('nullable', 'max:255'),
            DateTime::make('atmos_pinfl_searched_at')->sortable()->readonly(),
            DateTime::make('unired_humo_searched_at')->sortable()->readonly(),
            DateTime::make('unired_uzcard_searched_at')->sortable()->readonly(),
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
        return [];
    }
}
