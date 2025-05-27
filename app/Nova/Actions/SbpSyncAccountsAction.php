<?php

namespace App\Nova\Actions;

use App\Clients\UniredSbpQRPayClient;
use App\Services\QrPayService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Throwable;

class SbpSyncAccountsAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return 'Sync SBP Accounts';
    }

    public function fields(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        try {
            if ((new QrPayService(new UniredSbpQRPayClient))->syncAccounts()) {
                return Action::message(__('Accounts have been synchronized successfully.'));
            }

            return Action::message(__('Accounts have not been synchronized. Please try again later.'));
        } catch (Throwable $e) {
            Log::error($e);

            return Action::danger(__('An error occurred while synchronizing sbp accounts. Please try again later.'));
        }
    }
}
