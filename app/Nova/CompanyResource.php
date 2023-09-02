<?php

namespace App\Nova;

use App\Enums\Company\CompanyStatus;
use App\Models\Company;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Status;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class CompanyResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Company>
     */
    public static string $model = Company::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'subdomain'
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
            Text::make(__('nova.company.name'), 'name')->filterable(),
            Text::make(__('nova.company.owner_name'), 'owner_name')->filterable(),
            Text::make(__('nova.company.owner_phone'), 'owner_phone')->filterable(),
            Text::make(__('nova.company.owner_phone'), 'owner_phone')->filterable(),
            Status::make(__('nova.status'), 'status')->failedWhen([
                CompanyStatus::INACTIVE->value
            ])->loadingWhen([
                CompanyStatus::CREATED->value
            ])
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
