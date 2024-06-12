<?php

namespace App\Nova\Company;

use App\Models\Company\Feature;
use App\Models\Company\Module;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property Feature $resource
 */
class FeatureResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Feature>
     */
    public static string $model = Feature::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'description',
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
            Text::make(__('nova.feature.name'), 'name')->filterable()->exceptOnForms(),
            Text::make(__('nova.feature.description'), 'description')->filterable()->onlyOnDetail(),
            Currency::make(__('nova.feature.original_amount'), 'original_amount')->currency('UZS')->filterable()->exceptOnForms(),
            Currency::make(__('nova.feature.amount'), 'amount')->currency('UZS')->filterable(),
            Boolean::make(__('nova.feature.is_active'), 'is_active')->filterable()->showOnUpdating(function (NovaRequest $request, Feature $resource) {
                return (bool) $resource->modules->first(fn (Module $module) => $module->is_active);
            }),
            Code::make(__('nova.feature.original_data'), 'original_data')->json()->onlyOnDetail(),
            Code::make(__('nova.feature.data'), 'data')->json()->help($this->resource->description),
            DateTime::make(__('nova.feature.created_at'), 'created_at')->filterable()->exceptOnForms(),
            DateTime::make(__('nova.feature.updated_at'), 'updated_at')->filterable()->exceptOnForms(),

            BelongsToMany::make(__('nova.feature.modules'), 'modules', ModuleResource::class),
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
