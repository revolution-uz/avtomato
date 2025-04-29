<?php

namespace App\Nova\Company;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Main\SettingsTypeEnum;
use App\Models\Company\CompanySettings;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @mixin CompanySettings
 */
class CompanySettingsResource extends Resource
{
    /**
     * The model the resource corresponds to.
     */
    public static string|CompanySettings $model = CompanySettings::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name.value';

    /**
     * The columns that should be searched.
     *
     * @var array<int, string>
     */
    public static $search = [
        'name',
        'description',
        'type',
    ];

    public static function authorizedToCreate(Request $request): bool
    {
        return false;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, Field>
     */
    public function fields(NovaRequest $request): array
    {
        $response = [
            ID::make('Id')->sortable(),
            Text::make('Name')->displayUsing(fn () => $this->name->getLabel())->exceptOnForms(),
            Text::make('Description')->exceptOnForms(),
            Select::make('Type')->options(SettingsTypeEnum::getAllTypesForDropdown())->displayUsingLabels()->exceptOnForms(),
        ];

        $response[] = match ($this->type) {
            SettingsTypeEnum::INT => Number::make('Value')->rules(['required', 'integer']),
            SettingsTypeEnum::BOOLEAN => Boolean::make('Value')->rules(['required', 'boolean']),
            SettingsTypeEnum::FLOAT => Number::make('Value')->rules(['required', 'decimal']),
            SettingsTypeEnum::TEXT => Textarea::make('Value')->rules(['required', 'max:2014']),
            SettingsTypeEnum::DATETIME => DateTime::make('Value')->rules(['required', 'datetime']),
            SettingsTypeEnum::DATE => DateTime::make('Value')->rules(['required', 'date']),
            SettingsTypeEnum::FILE => $request->isResourceIndexRequest()
                ? Boolean::make('Value')
                : File::make('Value')
                    ->rules([
                        'required',
                        $this->name === CompanySettingsEnum::LOYALTY_PROGRAM_FIREBASE_PATH ? 'mimes:json' : 'mimes:mp4,avi,mov,mkv',
                        $this->name === CompanySettingsEnum::LOYALTY_PROGRAM_FIREBASE_PATH ? 'max:100' : 'max:51200',
                    ])
                    ->disk($this->name === CompanySettingsEnum::LOYALTY_PROGRAM_FIREBASE_PATH ? 'local' : 'public')
                    ->path($this->name === CompanySettingsEnum::LOYALTY_PROGRAM_FIREBASE_PATH ? 'credentials' : 'uploads/loyalty_program'),
            SettingsTypeEnum::STRING, null => Text::make('Value')->rules(['required', 'max:255']),
        };

        $response[] = DateTime::make('Updated at', 'updated_at')->exceptOnForms();

        return $response;
    }

    /**
     * Get the cards available for the request.
     *
     * @return array<object>
     */
    public function cards(Request $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<object>
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<object>
     */
    public function lenses(Request $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<object>
     */
    public function actions(Request $request): array
    {
        return [];
    }

    public function authorizedToDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToView(Request $request): bool
    {
        return false;
    }

    public function authorizedToForceDelete(Request $request): bool
    {
        return false;
    }

    public function authorizedToReplicate(Request $request): bool
    {
        return false;
    }
}
