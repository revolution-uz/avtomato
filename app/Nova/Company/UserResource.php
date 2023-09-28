<?php

namespace App\Nova\Company;

use App\Models\Company\User;
use App\Nova\Actions\ChangeUserPassword;
use App\Nova\Actions\LoginAsUser;
use App\Nova\Resource;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class UserResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<User>
     */
    public static $model = User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'username';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'username', 'first_name', 'last_name', 'phone'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Avatar::make('avatar', 'avatar_preview_path')
                ->rounded()
                ->disableDownload()
                ->maxWidth(50),

            Text::make('Username')
                ->sortable()
                ->rules('required'),

            Text::make('First Name')
                ->sortable()
                ->rules('required'),

            Text::make('Last Name')
                ->sortable()
                ->rules('required'),

            Boolean::make('Status', 'status'),

            Text::make('Phone')
                ->sortable()
                ->onlyOnDetail()
                ->rules('required'),

            Text::make('Language')
                ->sortable()
                ->onlyOnDetail()
                ->rules('required'),

            Code::make('Template Theme', 'template_theme')
                ->json()
                ->onlyOnDetail(),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return [
            (new LoginAsUser())->showInline(),
            (new ChangeUserPassword())->showInline(),
        ];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
}
