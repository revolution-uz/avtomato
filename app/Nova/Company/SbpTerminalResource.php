<?php

namespace App\Nova\Company;

use App\Models\SbpAccount;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property SbpAccount $resource
 */
class SbpTerminalResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<SbpAccount>
     */
    public static string $model = SbpAccount::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'company_id', 'terminal_id', 'name', 'account', 'filial_code', 'inn', 'status',
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
            Text::make(__('nova.sbp_terminal.name'), 'name')->filterable()->exceptOnForms(),
            Text::make(__('nova.sbp_terminal.filial_code'), 'filial_code')->filterable()->exceptOnForms(),
            Text::make(__('nova.sbp_terminal.account'), 'account')->filterable()->exceptOnForms(),
            Text::make(__('nova.sbp_terminal.inn'), 'inn')->filterable()->exceptOnForms(),
            DateTime::make(__('nova.sbp_terminal.created_at'), 'created_at')->filterable()->onlyOnDetail(),
            DateTime::make(__('nova.sbp_terminal.updated_at'), 'updated_at')->filterable()->onlyOnDetail(),
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
        return $this->resource->name;
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return true;
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
