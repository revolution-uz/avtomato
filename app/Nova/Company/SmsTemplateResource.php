<?php

namespace App\Nova\Company;

use App\Models\Company\SmsTemplate;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property SmsTemplate $resource
 */
class SmsTemplateResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<SmsTemplate>
     */
    public static string $model = SmsTemplate::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'original_template', 'template',
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
            Text::make(__('nova.template.name'), 'original_template')->filterable(),
            Text::make(__('nova.template.name'), 'template')->filterable(),
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
