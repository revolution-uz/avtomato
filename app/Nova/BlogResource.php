<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Slug;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @mixin \App\Models\Blog
 */
class BlogResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Blog>
     */
    public static $model = \App\Models\Blog::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title_uz';

    /**
     * The columns that should be searched.
     *
     * @var string[]
     */
    public static $search = [
        'id',
        'title_uz',
        'body_uz',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<Field>
     */
    public function fields(Request $request): array
    {
        $data = [
            ID::make()->sortable(),

            Image::make('image_en')
                ->creationRules('required')
                ->acceptedTypes('image/*')
                ->rules(
                    [Rule::requiredIf(function () use ($request) {
                        $resourceId = $this->resource?->getKey();

                        return is_null($resourceId) || ! is_null($request->input('image_en'));
                    }),
                        'max:50000',
                    ]
                ),
            Image::make('image_uz')->acceptedTypes('image/*')->rules(['nullable', 'max:50000']),
            Image::make('image_ru')->acceptedTypes('image/*')->rules(['nullable', 'max:50000']),

            Text::make('title')
                ->displayUsing(fn () => Str::limit($this->title_uz, 30))
                ->exceptOnForms()->sortable()
                ->showOnDetail(false),

            Text::make('body')->sortable()
                ->displayUsing(fn () => Str::limit($this->body_uz, 30))
                ->exceptOnForms()
                ->showOnDetail(false),

            Text::make('title_uz')->rules(['required', 'string'])->showOnIndex(false),
            Text::make('title_ru')->rules(['required', 'string'])->showOnIndex(false),
            Text::make('title_en')->rules(['required', 'string'])->showOnIndex(false),

            Markdown::make('body_uz')->withFiles()->rules(['required', 'string'])->hideFromIndex(),
            Markdown::make('body_ru')->withFiles()->rules(['required', 'string'])->hideFromIndex(),
            Markdown::make('body_en')->withFiles()->rules(['required', 'string'])->hideFromIndex(),

            Slug::make('slug')->creationRules('unique:blogs,slug')
                ->updateRules('unique:blogs,slug,{{resourceId}}')->from('title_en')
                ->rules('regex:/^[a-z0-9-]+$/')
                ->showOnIndex(false),

            DateTime::make('created_at')->showOnIndex(false)->exceptOnForms()->sortable(),

            BelongsTo::make('Category', 'category', BlogCategoryResource::class),
        ];

        foreach (config('app.locales') as $locale) {
            $data[] = Textarea::make("meta_description->message_{$locale}")
                ->rules(['required', 'string'])->hideFromIndex();
        }

        foreach (config('app.locales') as $locale) {
            $data[] = Text::make("meta_keywords->message_{$locale}")
                ->rules(['required', 'string'])->hideFromIndex();
        }

        return $data;
    }

    /**
     * Get the cards available for the request.
     *
     * @return array<object>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<object>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<object>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<object>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
