<?php

namespace App\Nova\Metrics;

use App\Models\Company;
use DateTimeInterface;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\MetricTableRow;
use Laravel\Nova\Metrics\Table;

class CompaniesMetric extends Table
{
    public function name()
    {
        return 'Companies with balance';
    }

    /**
     * Calculate the value of the metric.
     *
     * @return array<int, \Laravel\Nova\Metrics\MetricTableRow>
     */
    public function calculate(NovaRequest $request): array
    {
        $data = [];

        /** @var Company $company */
        foreach (Company::query()->orderBy('id')->get() as $company) {
            $data[] = MetricTableRow::make()
                ->icon('banknotes')
                ->iconClass('text-green-500')
                ->title($company->name . ' - ' . number_format($company->balanceFunds()->sum('amount'), thousands_separator: ' ') . ' UZS');
        }

        return $data;
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     */
    public function cacheFor(): ?DateTimeInterface
    {
        return now()->addMinutes(5);
    }
}
