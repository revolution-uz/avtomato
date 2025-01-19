<?php

namespace App\Nova\Company;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Models\Company\Installment;
use App\Nova\Resource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class InstallmentResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Installment>
     */
    public static $model = Installment::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Customer', 'customer', CustomerResource::class),
            BelongsTo::make('Branch', 'branch', BranchResource::class),
            BelongsTo::make('Tariff', 'tariff', TariffResource::class),
            BelongsTo::make('Admin', 'admin', CompanyAdminResource::class)->hideFromIndex(),
            Select::make('type')->options(InstallmentTypeEnum::options())->displayUsingLabels()->rules('required'),
            Currency::make('amount')->currency('UZS'),
            Currency::make('initial_fee')->currency('UZS')->hideFromIndex(),
            Number::make('percent')->displayUsing(fn ($value) => $value . '%'),
            Text::make('description')->hideFromIndex(),
            Boolean::make('sms_notify')->hideFromIndex(),
            Boolean::make('any_card_charge')->hideFromIndex(),
            Boolean::make('is_blacklist')->hideFromIndex(),
            Select::make('status')->options(InstallmentStatusEnum::options())->displayUsingLabels()->rules('required')->hideFromIndex(),
            Number::make('period'),
            Text::make('plum_id')->hideFromIndex(),
            DateTime::make('Canceled at', 'canceled_at')->hideFromIndex(),
            Text::make('cancellation_details')->hideFromIndex(),
            Date::make('Starts in', 'starts_in'),
            Number::make('card_after_due_days')->hideFromIndex(),
            Number::make('pinfl_after_due_days')->hideFromIndex(),
            Number::make('guarantor_after_due_days')->hideFromIndex(),
            Number::make('guarantor_pinfl_after_due_days')->hideFromIndex(),
            Currency::make('paid_amount')->currency('UZS'),
            DateTime::make('Created at', 'created_at'),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
