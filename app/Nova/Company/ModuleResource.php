<?php

namespace App\Nova\Company;

use App\Models\Company\Module;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property Module $resource
 */
class ModuleResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Module>
     */
    public static string $model = Module::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name',
    ];

    public static function authorizedToCreate(Request $request): bool
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
            Text::make(__('nova.module.name'), 'name')->filterable()->exceptOnForms(),
            Boolean::make(__('nova.module.is_active'), 'is_active')->filterable(),
            Currency::make(__('nova.module.original_amount'), 'original_amount')->currency('UZS')->filterable()->exceptOnForms(),
            Currency::make(__('nova.module.amount'), 'amount')->currency('UZS')->filterable(),
            DateTime::make(__('nova.module.created_at'), 'created_at')->filterable()->exceptOnForms(),
            DateTime::make(__('nova.module.updated_at'), 'updated_at')->filterable()->exceptOnForms(),

            BelongsToMany::make(__('nova.module.features'), 'features', FeatureResource::class),
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

    public function title(): string
    {
        return $this->resource->name->value;
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }

    public function authorizedToForceDelete(Request $request): bool
    {
        return false;
    }
}
