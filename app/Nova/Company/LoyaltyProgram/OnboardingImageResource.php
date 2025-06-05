<?php

namespace App\Nova\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\OnboardingImage;
use App\Nova\Resource;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;

class OnboardingImageResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<OnboardingImage>
     */
    public static string $model = OnboardingImage::class;

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
    public static $search = ['id'];

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Image::make('image_uz')
                ->acceptedTypes('image/*')
                ->rules('max:10240'),

            Image::make('image_ru')
                ->acceptedTypes('image/*')
                ->rules('max:10240'),

            Number::make('order', 'order')
                ->rules(['required', 'integer', 'min:1'])
                ->filterable(),

            Boolean::make('is_active', 'is_active')
                ->rules(['required', 'boolean'])
                ->default(true)
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
}
