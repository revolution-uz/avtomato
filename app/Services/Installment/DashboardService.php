<?php

namespace App\Services\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Helpers\Utilities;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    private ?string $transaction_by_type_start_date;
    private ?string $transaction_by_type_end_date;
    private ?string $installment_by_age_start_date;
    private ?string $installment_by_age_end_date;
    private ?int $branch_id;
    private Carbon $startOfMonth;

    public function __construct(
        ?string $transaction_by_type_start_date = null,
        ?string $transaction_by_type_end_date = null,
        ?string $installment_by_age_start_date = null,
        ?string $installment_by_age_end_date = null,
        ?int $branch_id = null,
    ) {
        $this->transaction_by_type_start_date = $transaction_by_type_start_date;
        $this->transaction_by_type_end_date = $transaction_by_type_end_date;
        $this->installment_by_age_start_date = $installment_by_age_start_date;
        $this->installment_by_age_end_date = $installment_by_age_end_date;
        $this->branch_id = $branch_id;
        $this->startOfMonth = Carbon::today()->startOfMonth();
    }

    public function getTransaction()
    {
        $transactionQuery = Transaction::query()->onlySuccess();

        return [
            'count' => $transactionQuery->clone()->count(),
            'current_month_count' => $transactionQuery->clone()->where('paid_at', '>=', $this->startOfMonth)->count(),
            'amount' => $transactionQuery->clone()->sum('amount'),
            'current_month_amount' => $transactionQuery->clone()->where('paid_at', '>=', $this->startOfMonth)->sum('amount'),
        ];
    }

    public function getInstallment()
    {
        $installmentQuery = Installment::query()->where('status', '>', InstallmentStatusEnum::INACTIVE);

        return [
            'amount' => $installmentQuery->clone()->sum('amount'),
            'current_month_amount' => $installmentQuery->clone()->where('created_at', '>=', $this->startOfMonth)->sum('amount'),
        ];
    }

    public function getInstallmentFutureProfit()
    {
        $query = Installment::query()->where('status', InstallmentStatusEnum::ACTIVE);

        return [
            'amount' => $query->sum(Installment::getProfitAmountRaw()),
            'current_month_amount' => $query->sum(DB::raw('(amount - initial_fee - paid_amount) * percent / 100 / period')),
        ];
    }

    public function getInstallmentOverdue()
    {
        $installmentOverdueCountQuery = Installment::query()->where('status', InstallmentStatusEnum::ACTIVE)->whereNextPayDate('<=', Carbon::now());
        $installmentOverdueBeforeWeekQuery = $installmentOverdueCountQuery->clone()->where('created_at', '<', Carbon::today()->startOfWeek());
        $installment_overdue_count = $installmentOverdueCountQuery->clone()->count();
        $installment_overdue_amount = $installmentOverdueCountQuery->clone()->sum('amount');
        $before_week_count = $installmentOverdueBeforeWeekQuery->clone()->where('created_at', '<', Carbon::today()->startOfWeek())->count();
        $before_week_amount = $installmentOverdueBeforeWeekQuery->clone()->where('created_at', '<', Carbon::today()->startOfWeek())->sum('amount');

        return [
            'count' => $installment_overdue_count,
            'count_increasing_percent' => max(0, Utilities::preventDivideByZero($installment_overdue_count, $before_week_count) * 100 - 100),
            'amount' => $before_week_amount,
            'amount_increasing_percent' => max(0, Utilities::preventDivideByZero($installment_overdue_amount, $before_week_amount) * 100 - 100),
        ];
    }

    public function getNpl()
    {
        return $this->calculateNpl(60, 7);
    }

    public function getEnpl()
    {
        return $this->calculateNpl(30, 7);
    }

    public function getInstallmentInfoByStatus(): array
    {
        $timestamps = Utilities::getTimestampsOfMonthOfThisYear();
        $result = [];
        foreach (InstallmentStatusEnum::cases() as $status) {
            $query = Installment::query()->where('status', $status);
            $data = [];
            for ($i = 0; $i < 12; $i++) {
                $data[] = $query->clone()->whereBetween('created_at', [
                    Carbon::createFromTimestamp($timestamps[$i]['start']),
                    Carbon::createFromTimestamp($timestamps[$i]['end']),
                ])->sum('amount');
            }
            $result[] = [
                'name' => __('api.installment_status_' . $status->name),
                'type' => 'line',
                'stack' => 'Total',
                'count' => $query->clone()->where('created_at', '>=', Carbon::today()->startOfYear())->countId(),
                'data' => $data,
            ];
        }

        return $result;
    }

    public function getInstallmentOverdues(): array
    {
        $result = [];
        $queryHasDebt = Installment::query()->where('status', InstallmentStatusEnum::ACTIVE)->whereHasDebt();
        $total_amount = $queryHasDebt->clone()->whereNextPayDate('<=', Carbon::now())->sum(Installment::getDebtAmountRaw());
        $total_count = $queryHasDebt->clone()->whereNextPayDate('<=', Carbon::now())->count();
        for ($i = 10; $i < 70; $i += 10) {
            $subQuery = $queryHasDebt->clone()->whereNextPayDate('>', Carbon::now()->subDays($i))->whereNextPayDate('<=', Carbon::now()->subDays($i - 10));
            $count = $subQuery->clone()->count();
            $amount = $subQuery->clone()->sum(Installment::getDebtAmountRaw());
            $total_count -= $count;
            $total_amount -= $amount;
            $result[] = [
                'overdue_days' => __('api.till :days days', [
                    'days' => $i,
                ]),
                'amount' => $amount,
                'count' => $count,
            ];
        }
        $result[] = [
            'overdue_days' => __('api.above :days days', [
                'days' => 60,
            ]),
            'amount' => $total_amount,
            'count' => $total_count,
        ];

        return $result;
    }

    public function getTransactionChart(): array
    {
        return Transaction::selectRaw('transaction_types.name_uz as label, SUM(transactions.amount) as value')
            ->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->where('transactions.status', 1)
            ->when(
                $this->transaction_by_type_start_date && $this->transaction_by_type_end_date,
                fn ($query) => $query
                    ->whereBetween('transactions.paid_at', [
                        $this->transaction_by_type_start_date, $this->transaction_by_type_end_date,
                    ]))
            ->groupBy('transactions.transaction_type_id', 'transaction_types.name_uz')
            ->orderBy('transactions.transaction_type_id')
            ->get()
            ->toArray();
    }

    public function getActiveInstallmentsByAge()
    {
        return $this->ageBasedInstallments();
    }

    public function getOverdueInstallmentsByAge()
    {
        return $this->ageBasedInstallments(true);
    }

    private function calculateNpl(int $overdueDays, int $beforeDays): array
    {
        $query = Installment::query();
        $nplQuery = $query->clone()->whereNextPayDate('<', Carbon::now()->subDays($overdueDays));
        $nplBeforeWeekQuery = $query->clone()->whereNextPayDate('<', Carbon::now()->subDays($overdueDays - $beforeDays));

        $installment_total_amount = (float) $query->clone()->sum(Installment::getInstallmentAmountRaw());
        $npl = $installment_total_amount > 0 ? $nplQuery->sum(Installment::getProfitAmountRaw()) / $installment_total_amount * 100 : 0;
        $nplBefore = $installment_total_amount > 0 ? $nplBeforeWeekQuery->sum(Installment::getProfitAmountRaw()) / $installment_total_amount * 100 : 0;

        return [
            'value' => $npl,
            'value_of_last_week' => $nplBefore,
            'value_increasing_percent' => max(0, Utilities::preventDivideByZero($npl, $nplBefore) * 100 - 100),
        ];
    }

    private function ageBasedInstallments(bool $overdue = false): array
    {
        $results = Customer::selectRaw('
            CASE
                WHEN birth_date IS NULL THEN \'unknown\'
                WHEN EXTRACT(YEAR FROM AGE(birth_date)) BETWEEN 18 AND 25 THEN \'18-25\'
                WHEN EXTRACT(YEAR FROM AGE(birth_date)) BETWEEN 26 AND 33 THEN \'26-33\'
                WHEN EXTRACT(YEAR FROM AGE(birth_date)) BETWEEN 34 AND 40 THEN \'34-40\'
                WHEN EXTRACT(YEAR FROM AGE(birth_date)) BETWEEN 41 AND 49 THEN \'41-49\'
                WHEN EXTRACT(YEAR FROM AGE(birth_date)) BETWEEN 50 AND 59 THEN \'50-59\'
                ELSE \'60-others\'
            END AS label,
            COUNT(installments.id) as value
        ')
            ->leftJoin('installments', 'customers.id', '=', 'installments.customer_id')
            ->when(! $overdue, fn ($q) => $q
                ->when(
                    $this->installment_by_age_start_date && $this->installment_by_age_end_date,
                    fn ($q) => $q
                        ->whereBetween('installments.created_at', [
                            $this->installment_by_age_start_date, $this->installment_by_age_end_date,
                        ])))
            ->when($overdue, fn ($q) => $q
                ->where('installments.status', InstallmentStatusEnum::ACTIVE->value)
                ->when(
                    $this->installment_by_age_start_date && $this->installment_by_age_end_date,
                    fn ($q) => $q
                        ->whereBetween(Installment::getNextPayDateRaw(), [
                            $this->installment_by_age_start_date, $this->installment_by_age_end_date,
                        ]))
                ->where(Installment::getNextPayDateRaw(), '<', Carbon::now()))
            ->when($this->branch_id, fn ($q) => $q
                ->where('installments.branch_id', $this->branch_id))
            ->groupBy('label')
            ->get();

        $ageRanges = collect([
            ['label' => 'unknown'],
            ['label' => '18-25'],
            ['label' => '26-33'],
            ['label' => '34-40'],
            ['label' => '41-49'],
            ['label' => '50-59'],
            ['label' => '60-others'],
        ]);

        return $ageRanges->map(fn ($range) => [
            'label' => $range['label'],
            'value' => $results->firstWhere('label', $range['label'])->value ?? 0,
        ])
            ->filter(fn ($item) => $item['label'] !== 'unknown' || $item['value'] !== 0)
            ->toArray();
    }
}
