<?php

namespace App\Nova\Actions;

use App\Clients\UniredSbpQRPayClient;
use App\Models\Company\SbpTransaction;
use App\Services\CompanyQrPayService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Throwable;

class SbpTransactionRefreshAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return 'Refresh ';
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
        /** @var SbpTransaction $model */
        $model = $models->first();
        try {
            $service = new CompanyQrPayService(new UniredSbpQRPayClient);
            $service->statePayment($model->ext_id);

            return Action::message(__('Transaction has been refreshed'));
        } catch (Throwable $e) {
            Log::error($e);

            return Action::danger(__('An error occurred while refreshing sbp transaction. Please try again later.'));
        }
    }
}
