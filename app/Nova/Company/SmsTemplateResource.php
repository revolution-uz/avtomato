<?php

namespace App\Nova\Company;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\SmsTemplateStatusEnum;
use App\Models\Company\SmsTemplate;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
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
        return true;
    }

    /**
     * Get the fields displayed by the resource.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Select::make(__('nova.status'), 'status')
                ->options(CompanyStatusEnum::getAllCasesForDropdown())
                ->displayUsingLabels()
                ->default(SmsTemplateStatusEnum::DRAFT)
                ->required(),
            Text::make(__('nova.original_template'), 'original_template')
                ->filterable()
                ->required(),
            Text::make(__('nova.template'), 'template')
                ->filterable(),
            Code::make(__('nova.data'), 'data')
                ->json()
                ->help('{"category": "debt-templates", "overdue_days": 0}'),
            Text::make(__('nova.description'), 'description')
                ->filterable()
                ->displayUsing(fn ($item) => Str::limit($item, 50))
                ->required(),
            DateTime::make(__('nova.created_at'), 'created_at')
                ->filterable()
                ->onlyOnDetail(),
            DateTime::make(__('nova.updated_at'), 'updated_at')
                ->filterable()
                ->onlyOnDetail(),
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
        return $this->resource->description;
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
