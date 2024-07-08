<?php

namespace App\Nova\Actions;

use App\Models\Company\CompanyAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ChangeUserPassword extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     */
    public function handle(ActionFields $fields, Collection $models): mixed
    {
        /* @var $admin CompanyAdmin */
        $admin = $models->first();
        $admin->password = bcrypt($fields->get('password'));
        if ($admin->save()) {
            return Action::message(__('Password changed'));
        }

        return Action::danger(__('Can\'t change password'));
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Text::make(__('Password'), 'password')
                ->rules('required', 'string', 'min:8'),
        ];
    }
}
