<?php

namespace App\Nova;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
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
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'owner_name', 'owner_phone',
    ];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('nova.company.name'), 'name')
                ->rules('required', 'max:32')
                ->creationRules('unique:companies,name')
                ->updateRules('unique:companies,name,{{resourceId}}')
                ->filterable(),
            Text::make(__('nova.company.owner_name'), 'owner_name')
                ->rules('required', 'max:32')
                ->filterable(),
            Text::make(__('nova.company.owner_phone'), 'owner_phone')
                ->rules('required', 'max:16')
                ->filterable(),
            Text::make(__('nova.company.address'), 'address')
                ->creationRules('required')
                ->rules('max:255')
                ->filterable(),
            Text::make(__('nova.company.bank_name'), 'bank_name')
                ->rules('max:255')
                ->creationRules('required')
                ->filterable(),
            Text::make(__('nova.company.bank_code'), 'bank_code')
                ->creationRules('required')
                ->updateRules('nullable')
                ->rules('regex:/^[0-9]+$/', 'max:255')
                ->filterable(),
            Text::make(__('nova.company.account_number'), 'account_number')
                ->creationRules('required')
                ->updateRules('nullable')
                ->rules('regex:/^[0-9]+$/', 'max:255')
                ->filterable(),
            Text::make(__('nova.company.tin'), 'tin')
                ->creationRules('required')
                ->updateRules('nullable')
                ->rules('regex:/^[0-9]+$/', 'max:255')
                ->filterable(),
            Select::make(__('nova.status'), 'status')
                ->options(CompanyStatusEnum::getAllCasesForDropdown())
                ->displayUsingLabels()
                ->default(CompanyStatusEnum::CREATED),
            HasMany::make('Domains', 'domains', DomainResource::class),
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
}
