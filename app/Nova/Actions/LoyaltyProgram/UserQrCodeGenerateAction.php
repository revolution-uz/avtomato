<?php

namespace App\Nova\Actions\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class UserQrCodeGenerateAction extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * The displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return 'Generate QR Code';
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
        /** @var User $model */
        $model = $models->first();
        Action::message(__($model->getQrAttribute(3600)['qr'] ?? ''));
    }
}
