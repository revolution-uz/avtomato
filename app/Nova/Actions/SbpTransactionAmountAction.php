<?php

namespace App\Nova\Actions;

use App\Clients\UniredSbpQRPayClient;
use App\Services\CompanyQrPayService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Throwable;

class SbpTransactionAmountAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return 'Create SBP Transaction';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            Number::make('Amount', 'amount')->rules('required', 'integer'),
        ];
    }

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $amount = $fields->get('amount');
        try {
            $service = new CompanyQrPayService(new UniredSbpQRPayClient);
            $service->createPayment($amount);

            return Action::message(__('Transaction has been created'));
        } catch (Throwable $e) {
            Log::error($e);

            return Action::danger(__('An error occurred while creating sbp transaction. Please try again later.'));
        }
    }
}
