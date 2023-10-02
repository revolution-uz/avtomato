<?php

namespace App\Nova\Company;

use App\Models\Company\Tariff;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class TariffsResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Tariff>
     */
    public static $model = Tariff::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'is_annual', 'percent', 'min_amount', 'max_amount', 'min_initial_fee', 'max_initial_fee',
    ];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),
            Boolean::make('Is Annual')
                ->sortable()
                ->rules('required'),
            Number::make('Percent', 'percent')
                ->sortable()
                ->rules('required')
                ->filterable(),
            Number::make('Min amount', 'min_amount')
                ->sortable()
                ->rules('required')
                ->filterable(),
            Number::make('Max amount', 'max_amount')
                ->sortable()
                ->rules('required')
                ->filterable(),
            Number::make('Min initial fee', 'min_initial_fee')
                ->sortable()
                ->rules('required')
                ->filterable(),
            Number::make('Max initial fee', 'max_initial_fee')
                ->sortable()
                ->rules('required')
                ->filterable(),
        ];
    }

    /**
     * Get the cards available for the request.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     */
    public function actions(NovaRequest $request): array
    {
        return [];
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
}
