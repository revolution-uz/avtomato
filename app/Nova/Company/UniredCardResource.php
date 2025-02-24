<?php

namespace App\Nova\Company;

use App\Enums\Company\CardTypeEnum;
use App\Models\Company\UniredCard;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property UniredCard $resource
 */
class UniredCardResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<UniredCard>
     */
    public static string $model = UniredCard::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'pinfl',
        'uuid',
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
            BelongsTo::make('customer', 'customer', CustomerResource::class),
            Text::make('pinfl')->sortable(),
            Text::make('uuid')->sortable(),
            Boolean::make('auto')->sortable(),
            Boolean::make('is_verified')->sortable(),
            Boolean::make('is_blocked')->sortable(),
            Text::make('block_reason')->hideFromIndex(),
            DateTime::make('blocked_at')->sortable(),
            Text::make('owner')->sortable(),
            Text::make('pan')->sortable(),
            Text::make('phone')->sortable(),
            Select::make('type')->options(CardTypeEnum::getAllCasesForDropdown())->displayUsingLabels()->sortable(),
            DateTime::make('created_at')->sortable(),
            DateTime::make('updated_at')->sortable(),
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
