<?php

namespace App\Nova\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Resource;

/**
 * @property int $id
 * @property string $phone
 *
 * @mixin User
 */
class SubscriptionResource extends Resource
{
    public static $model = Subscription::class;

    public static $title = 'user.phone';

    public static $search = [
        'tariff_id', 'is_active',
    ];

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            Number::make('Tariff Id', 'tariff_id')->sortable()->rules('required', 'max:255')->onlyOnForms(),
            Number::make('User Id', 'user_id')->sortable()->rules('required', 'max:255')->onlyOnForms(),
            Text::make('Phone', 'user.phone')->sortable()->rules('required', 'max:255')
                ->resolveUsing(function ($value) {
                    return $this->user->phone;
                })->exceptOnForms(),
            Text::make('First Name', 'user.first_name')->sortable()->rules('nullable', 'max:255')
                ->resolveUsing(function ($value) {
                    return $this->user->first_name;
                })->exceptOnForms(),
            Text::make('Last Name', 'user.last_name')->sortable()->rules('nullable', 'max:255')
                ->resolveUsing(function ($value) {
                    return $this->user->last_name;
                })->exceptOnForms(),
            Text::make('Tariff Name', 'tariff.name')->sortable()->rules('nullable', 'max:255')
                ->resolveUsing(function ($value) {
                    return $this->tariff->name;
                })->exceptOnForms(),
            DateTime::make('Expired At', 'expired_at')->sortable()->rules('nullable', 'date'),
            Boolean::make('Is Active', 'is_active')->sortable()->rules('required', 'boolean')->default(true),
            Number::make('Remaining Limit Amount', 'remaining_limit_amount')->sortable()->rules('required', 'integer'),
            Boolean::make('Is Available', 'is_available')->onlyOnDetail(),
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
