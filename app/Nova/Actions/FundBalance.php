<?php

namespace App\Nova\Actions;

use App\Models\Company\BalanceFund;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Throwable;

class FundBalance extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $amount = $fields->get('amount');
        try {
            BalanceFund::query()->create([
                'amount' => $amount,
                'admin_id' => auth()->id(),
            ]);

            return Action::message(__('Balance has been funded'));
        } catch (Throwable $e) {
            Log::error($e);

            return Action::danger(__('An error occurred while funding the balance. Please try again later.'));
        }
    }

    /**
     * Get the fields available on the action.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Number::make('Amount', 'amount')->rules('required', 'integer'),
        ];
    }
}
