<?php

namespace App\Nova;

use App\Models\BaseSmsTemplate;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property BaseSmsTemplate $resource
 */
class BaseSmsTemplateResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<BaseSmsTemplate>
     */
    public static string $model = BaseSmsTemplate::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'original_template',
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
//            Heading::make($this->resource->description),
            ID::make()->sortable(),
            Text::make(__('nova.template.original_template'), 'original_template')->filterable()->exceptOnForms(),
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

//    public function title(): string
//    {
//        return $this->resource->name->value;
//    }

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
