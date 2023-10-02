<?php

namespace App\Nova\Actions;

use App\Models\Company\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Throwable;

class LoginAsUser extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        /* @var $user User */
        $user = $models->first();
        try {
            $token = $user->createToken('Nova');

            return Action::openInNewTab(url('/') . '/#/login?token=' . substr($token->plainTextToken, strpos($token->plainTextToken, '|') + 1));
        } catch (Throwable $e) {
            Log::error($e);

            return Action::danger(__("Can't generate access token"));
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}
