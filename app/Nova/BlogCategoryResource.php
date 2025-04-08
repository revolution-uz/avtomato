<?php

namespace App\Nova;

use App\Models\BlogCategory;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @mixin BlogCategory
 */
class BlogCategoryResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<BlogCategory>
     */
    public static string $model = BlogCategory::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name_uz';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'name_ru',
        'name_en',
        'name_uz',
    ];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        $data = [
            ID::make()->sortable(),
            Text::make('name_en')->rules(['required', 'string'])->showOnIndex(false),
            Text::make('name_ru')->rules(['required', 'string'])->showOnIndex(false),
            Text::make('name_uz')->rules(['required', 'string'])->showOnIndex(false),
            Slug::make('slug')->from('name_en')->rules('regex:/^[a-z0-9-]+$/')->showOnIndex(false),
        ];

        foreach (config('app.locales') as $locale) {
            $data[] = Textarea::make("meta_description->message_{$locale}")->rules(['required', 'string']);
        }
        foreach (config('app.locales') as $locale) {
            $data[] = Text::make("meta_keywords->message_{$locale}")->rules(['required', 'string']);
        }

        return $data;
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
