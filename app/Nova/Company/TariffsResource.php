<?php

namespace App\Nova\Company;

use App\Models\Company\Tariffs;
use App\Nova\Resource;
use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class TariffsResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Tariffs>
     */
    public static $model = Tariffs::class;

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
        'id', 'name', 'isAnnual', 'percent', 'min_amount', 'max_amount', 'min_initial_fee', 'max_initial_fee'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),
            Text::make('Is Annual', 'isAnnual')->filterable(),
            Text::make('Percent', 'percent')->filterable(),
            Text::make('Min amount', 'min_amount')->filterable(),
            Text::make('Max amount', 'max_amount')->filterable(),
            Text::make('Min initial fee', 'min_initial_fee')->filterable(),
            Text::make('Max initial fee', 'max_initial_fee')->filterable(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
