<?php

namespace App\Nova\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

/**
 * @property int $id
 * @property string $phone
 *
 * @mixin User
 */
class UserResource extends Resource
{
    public static $model = User::class;

    public static $title = 'phone';

    public static $search = [
        'phone', 'first_name', 'last_name', 'birth_date',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Phone', 'phone')->sortable()->rules('required', 'max:255'),
            Text::make('First Name', 'first_name')->sortable()->rules('nullable', 'max:255'),
            Text::make('Last Name', 'first_name')->sortable()->rules('nullable', 'max:255'),
            Date::make('Birth Date', 'birth_date')->sortable()->rules('nullable', 'date'),
            Text::make('QR Code', 'main_device')->onlyOnDetail()->asHtml()
                ->resolveUsing(function ($value) {
                    return $this->getQrAttribute(3600)['id'];
                }),
        ];
    }

    public function cards(Request $request): array
    {
        return [];
    }

    public function filters(Request $request): array
    {
        return [];
    }

    public function lenses(Request $request): array
    {
        return [];
    }

    public function actions(Request $request): array
    {
        return [];
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }
}
