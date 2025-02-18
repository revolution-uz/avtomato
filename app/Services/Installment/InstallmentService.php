<?php

namespace App\Services\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Helpers\Utilities;
use App\Models\Company\Branch;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Tariff;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class InstallmentService
{
    private BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public static function generatePeriods(?Carbon $startDate, $period): array
    {
        if (! $startDate) {
            $startDate = Carbon::now();
        }

        $dates = [];
        for ($i = 0; $i < $period; $i++) {
            $currentDate = $startDate->copy()->addMonths($i);
            if ($currentDate->day != $startDate->day) {
                $currentDate = $currentDate->subMonth()->endOfMonth();
            }
            $dates[] = $currentDate;
        }

        return $dates;
    }

    public static function getDebtorInstallments(string|array $overdueDays): Collection
    {
        $nextPaymentDate = "(starts_in + (FLOOR(paid_amount / ROUND(((amount - initial_fee) * (percent / 100 + 1) / period)::numeric, 2))::text || ' month')::interval)";

        return Installment::query()
            ->where('status', InstallmentStatusEnum::ACTIVE)
            ->whereRaw("EXTRACT(DAY FROM CURRENT_DATE - {$nextPaymentDate}) IN ({$overdueDays})")
            ->get();
    }

    public function create(array $attributes): Installment
    {
        $attributes['status'] = InstallmentStatusEnum::INACTIVE;
        $attributes['admin_id'] = auth()->user()->id;
        $attributes['sms_notify'] = false;
        $attributes['any_card_charge'] = false;
        $attributes['type'] = InstallmentTypeEnum::WITHOUT_CARD;
        $attributes['percent'] = Tariff::findOrFail($attributes['tariff_id'])->percent;

        return Installment::create($attributes);
    }

    /**
     * 01
     * 10
     *
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleAutoAndAnyCardCharge(Installment $installment, ?bool $auto_charge, ?bool $any_card_charge): Installment
    {
        if ($auto_charge !== null) {
            if ($auto_charge && ! ($installment->type->value & InstallmentTypeEnum::WITH_CARD->value)) {
                $installment->type = InstallmentTypeEnum::from($installment->type->value | InstallmentTypeEnum::WITH_CARD->value);
                if (! $this->withdrawAutoCharge($installment)) {
                    throw ValidationException::withMessages(['type' => __('api.You do not have enough balance to update installment type')]);
                }
            }
        }
        if ($any_card_charge !== null) {
            if ($any_card_charge && ! ($installment->type->value & InstallmentTypeEnum::WITH_PINFL->value)) {
                $installment->type = InstallmentTypeEnum::from($installment->type->value | InstallmentTypeEnum::WITH_PINFL->value);
                $installment->any_card_charge = $any_card_charge;
                if (! $this->withdrawAnyCardCharge($installment)) {
                    throw ValidationException::withMessages(['type' => __('api.You do not have enough balance to update installment type')]);
                }
            }
            $installment->any_card_charge = $any_card_charge;
        }
        $installment->saveOrFail();

        return $installment;
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleGuarantorAutoCharge(InstallmentGuarantor $installmentGuarantor, bool $any_card_charge): InstallmentGuarantor
    {
        $installmentGuarantor->any_card_charge = $any_card_charge;
        if (! $any_card_charge) {
            $installmentGuarantor->saveOrFail();

            return $installmentGuarantor;
        }
        $this->toggleAutoAndAnyCardCharge($installmentGuarantor->installment, null, true);
        $installmentGuarantor->saveOrFail();

        return $installmentGuarantor;
    }

    /**
     * @throws Throwable
     */
    public function changeStatus(Installment $installment, InstallmentStatusEnum $status): bool
    {
        if ($status == InstallmentStatusEnum::ACTIVE) {
            if ($installment->isPaidInitialFee()) {
                $installment->status = $status;

                return $installment->save();
            }
            throw ValidationException::withMessages(['status' => __('api.You cannot activate an installment without withdrawing initial fee')]);
        }

        if ($status == InstallmentStatusEnum::CLOSED) {
            if (! $installment->is_fully_paid) {
                throw ValidationException::withMessages(['status' => __('api.You cannot close an installment without withdrawing amount')]);
            }
        }

        $installment->status = $status;

        return $installment->save();
    }

    public function getDashboardInfo(): array
    {
        return [
            'installment_amounts' => $this->getInstallmentAmounts(),
            'installment_info_by_status' => $this->getInstallmentInfoByStatus(),
            'installment_overdues' => $this->getInstallmentOverdues(),
        ];
    }

    public function getStatisticsForRange(string $from, string $to, ?array $branches = null): array
    {
        $branches_in_query = empty($branches) ? '' : 'AND branch_id IN (' . implode(',', $branches) . ')';

        return DB::select("
            WITH
                date_series AS (SELECT GENERATE_SERIES('{$from}'::date, '{$to}'::date, '1 day'::interval)::date AS date),
                installment_precalculations AS (
                    SELECT (amount - initial_fee) * (percent / 100 + 1) / period AS monthly_payment,
                           starts_in + (ROUND(DATE_PART('month', AGE(ds.date::date, starts_in)) + DATE_PART('day', AGE(ds.date::date, starts_in)) / 30) || ' month')::interval AS payment_date,
                           GREATEST((amount - initial_fee) * (percent / 100 + 1) / period * LEAST(ROUND(DATE_PART('month', AGE(ds.date, starts_in)) + DATE_PART('day', AGE(ds.date, starts_in)) / 30) + 1, period) - paid_amount, 0) AS debt_amount
                    FROM installments
                        INNER JOIN date_series ds
                            ON starts_in + (ROUND(DATE_PART('month', AGE(ds.date::date, starts_in)) + DATE_PART('day', AGE(ds.date::date, starts_in)) / 30) || ' month')::interval = ds.date::date
                                AND ROUND(DATE_PART('month', AGE(ds.date::date, starts_in)) + DATE_PART('day', AGE(ds.date::date, starts_in)) / 30) BETWEEN 0 AND period - 1
                    WHERE status IN (1,2) {$branches_in_query}
                ),
                installment_calculations AS (
                    SELECT payment_date,
                        LEAST(debt_amount, monthly_payment) AS debt_amount,
                        ABS(LEAST(debt_amount, monthly_payment) - monthly_payment) as paid_amount
                    FROM installment_precalculations
                )
            SELECT
                EXTRACT(EPOCH  FROM date) AS date, COALESCE(SUM(debt_amount), 0) AS debt_amount_sum, COALESCE(SUM(paid_amount), 0) AS paid_amount_sum
            FROM date_series ds
                LEFT JOIN installment_calculations ic ON ds.date = ic.payment_date
            GROUP BY ds.date
            ORDER BY ds.date
        ");
    }

    public function getStatisticsForDate(string $date, ?array $branches = null): array
    {
        $branches_in_query = empty($branches) ? '' : 'AND branch_id IN (' . implode(',', $branches) . ')';

        return DB::select("
            WITH installments_optimized AS (
                SELECT
                    id, amount, initial_fee, percent, period, paid_amount, starts_in, branch_id, customer_id,
                    (amount - initial_fee) * (percent / 100 + 1) / period AS monthly_payment,
                    ROUND(DATE_PART('month', AGE('{$date}'::date, starts_in)) + DATE_PART('day', AGE('{$date}'::date, starts_in)) / 30) AS age
                FROM installments WHERE status IN (1,2) {$branches_in_query}
            )
            SELECT
                i.id,
                b.name AS branch_name,
                c.last_name || ' ' || c.first_name || ' ' || c.middle_name AS customer_fullname,
                LEAST(GREATEST(i.monthly_payment * LEAST(i.age + 1, i.period) - i.paid_amount, 0), i.monthly_payment) AS debt_amount,
                ABS(LEAST(GREATEST(i.monthly_payment * LEAST(i.age + 1, i.period) - i.paid_amount, 0), i.monthly_payment) - i.monthly_payment) as paid_amount
            FROM installments_optimized i
                LEFT JOIN branches b ON b.id = i.branch_id
                LEFT JOIN customers c ON c.id = i.customer_id
            WHERE i.starts_in + (i.age || ' month')::interval = '{$date}'::date
              AND i.age BETWEEN 0 AND i.period - 1
            ORDER BY i.id
        ");
    }

    public function statisticsByBranches(?Carbon $from_date = null, ?Carbon $to_date = null, ?array $branch_ids = null): array
    {
        $branches = Branch::query()
            ->when(! empty($branch_ids), fn ($q) => $q->whereIn('id', $branch_ids))
            ->get();
        $transactions = Transaction::query()
            ->select('branch_id', 'transaction_type_id', DB::raw('SUM(amount) as total_amount'))
            ->when(! empty($branch_ids), fn ($q) => $q->whereIn('branch_id', $branch_ids))
            ->when(! empty($from_date), fn ($q) => $q->whereDate('created_at', '>=', $from_date))
            ->when(! empty($to_date), fn ($q) => $q->whereDate('created_at', '<=', $to_date))
            ->where('status', TransactionStatusEnum::SUCCESS)
            ->groupBy('branch_id', 'transaction_type_id')
            ->orderBy('branch_id')
            ->get();
        $transactionTypes = TransactionType::all();
        $statistics = [];
        $totalAmountByType = [];
        $fullTotalAmount = 0;
        $formattedTotalAmountByType = [];
        foreach ($transactionTypes as $type) {
            $totalAmountByType[$type->id] = 0;
        }
        foreach ($branches as $branch) {
            $statistics[$branch->id] = [
                'branch_id' => $branch->id,
                'branch' => $branch,
                'types' => [],
                'total_amount_by_branch' => 0,
            ];

            foreach ($transactionTypes as $case) {
                $statistics[$branch->id]['types'][$case->id] = [
                    'name' => $case->name,
                    'amount' => 0,
                ];
            }
        }
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $branchId = $transaction->branch_id;
            $type = $transaction->transaction_type_id;
            /* @phpstan-ignore-next-line * */
            $totalAmount = $transaction->total_amount;

            $statistics[$branchId]['types'][$type]['amount'] += $totalAmount;

            $statistics[$branchId]['total_amount_by_branch'] += $totalAmount;
            if (! isset($totalAmountByType[$type])) {
                $totalAmountByType[$type] = 0;
            }
            $totalAmountByType[$type] += $totalAmount;

            $formattedTotalAmountByType[$type] = [
                'name' => $transaction->transactionType->name,
                'amount' => $totalAmountByType[$type],
            ];

            $fullTotalAmount += $totalAmount;
        }
        foreach ($transactionTypes as $case) {
            if (! isset($formattedTotalAmountByType[$case->id])) {
                $formattedTotalAmountByType[$case->id] = [
                    'name' => $case->name,
                    'amount' => 0,
                ];
            }
        }

        return [
            'branches' => $statistics,
            'total_amount_by_type' => array_values($formattedTotalAmountByType),
            'full_total_amount' => number_format($fullTotalAmount, 2, '.', ' '),
        ];
    }

    private function withdrawAutoCharge(Installment $installment): bool
    {
        if ($this->balanceService->isAvailableCompany()) {
            $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE, $installment);

            return true;
        }

        return false;
    }

    private function withdrawAnyCardCharge(Installment $installment): bool
    {
        /** @var Feature $feature */
        $feature = Feature::query()->where('name', FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->firstOrFail();

        $amount = $feature->chargeable_amount * ceil($installment->period / 3);

        if ($this->balanceService->isAvailableCompany()) {
            $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL, $installment, $amount);

            return true;
        }

        return false;
    }

    private function getInstallmentAmounts(): array
    {
        $installment_amounts = [];
        $start_of_month = Carbon::today()->startOfMonth();

        $transactionQuery = Transaction::query()->onlySuccess();

        $installment_amounts['transaction']['count'] = $transactionQuery->clone()->count();
        $installment_amounts['transaction']['current_month_count'] = $transactionQuery->clone()->where('paid_at', '>=', $start_of_month)->count();
        $installment_amounts['transaction']['amount'] = $transactionQuery->clone()->sum('amount');
        $installment_amounts['transaction']['current_month_amount'] = $transactionQuery->clone()->where('paid_at', '>=', $start_of_month)->sum('amount');

        $installmentQuery = Installment::query()->where('status', '>', InstallmentStatusEnum::INACTIVE);

        $installment_amounts['installment']['amount'] = $installmentQuery->clone()->sum('amount');
        $installment_amounts['installment']['current_month_amount'] = $installmentQuery->clone()->where('created_at', '>=', $start_of_month)->sum('amount');

        $installment_amounts['installment_future_profit']['amount'] = Installment::query()->where('status', InstallmentStatusEnum::ACTIVE)->sum(Installment::getProfitAmountRaw());
        $installment_amounts['installment_future_profit']['current_month_amount'] = Transaction::query()
            ->leftJoin('installments', 'transactions.installment_id', '=', 'installments.id')
            ->where('transactions.status', TransactionStatusEnum::SUCCESS)
            ->where('transactions.paid_at', '>=', $start_of_month)
            ->where('transactions.is_initial_fee', false)
            ->sum(DB::raw('transactions.amount * installments.percent / 100'));

        $installmentOverdueCountQuery = Installment::query()->where('status', InstallmentStatusEnum::ACTIVE)->whereNextPayDate('<', Carbon::now());
        $installmentOverdueBeforeWeekQuery = $installmentOverdueCountQuery->clone()->where('created_at', '<', Carbon::today()->startOfWeek());
        $installment_overdue_count = $installmentOverdueCountQuery->clone()->count();
        $installment_overdue_amount = $installmentOverdueCountQuery->clone()->sum('amount');
        $before_week_count = $installmentOverdueBeforeWeekQuery->clone()->where('created_at', '<', Carbon::today()->startOfWeek())->count();
        $before_week_amount = $installmentOverdueBeforeWeekQuery->clone()->where('created_at', '<', Carbon::today()->startOfWeek())->sum('amount');

        $installment_amounts['installment_overdue']['count'] = $installment_overdue_count;
        $installment_amounts['installment_overdue']['count_increasing_percent'] = max(0, Utilities::preventDivideByZero($installment_overdue_count, $before_week_count) * 100 - 100);
        $installment_amounts['installment_overdue']['amount'] = $installment_overdue_count;
        $installment_amounts['installment_overdue']['amount_increasing_percent'] = max(0, Utilities::preventDivideByZero($installment_overdue_amount, $before_week_amount) * 100 - 100);

        $installment_amounts['npl'] = $this->calculateNpl(60, 7);
        $installment_amounts['enpl'] = $this->calculateNpl(30, 7);

        return $installment_amounts;
    }

    private function calculateNpl(int $overdueDays, int $beforeDays): array
    {
        $query = Installment::query();
        $installment_total_amount = (float) Installment::query()->sum(Installment::getInstallmentAmountRaw());
        $nplQuery = (clone $query)->whereNextPayDate('<', Carbon::now()->subDays($overdueDays));
        $nplBeforeWeekQuery = (clone $query)->whereNextPayDate('<', Carbon::now()->subDays($overdueDays - $beforeDays));
        $npl = $installment_total_amount > 0 ? $nplQuery->sum(Installment::getProfitAmountRaw()) / $installment_total_amount * 100 : 0;
        $nplBefore = $installment_total_amount > 0 ? $nplBeforeWeekQuery->sum(Installment::getProfitAmountRaw()) / $installment_total_amount * 100 : 0;

        return [
            'value' => $npl,
            'value_of_last_week' => $nplBefore,
            'value_increasing_percent' => max(0, Utilities::preventDivideByZero($npl, $nplBefore) * 100 - 100),
        ];
    }

    private function getInstallmentInfoByStatus(): array
    {
        $timestamps = Utilities::getTimestampsOfMonthOfThisYear();
        $result = [];
        foreach (InstallmentStatusEnum::cases() as $status) {
            $query = Installment::query()->where('status', $status);
            $data = [];
            for ($i = 0; $i < 12; $i++) {
                $data[] = (clone $query)->whereBetween('created_at', [
                    Carbon::createFromTimestamp($timestamps[$i]['start']),
                    Carbon::createFromTimestamp($timestamps[$i]['end']),
                ])->sum('amount');
            }
            $result[] = [
                'name' => __('api.installment_status_' . $status->name),
                'type' => 'line',
                'stack' => 'Total',
                'count' => (clone $query)->where('created_at', '>=', Carbon::today()->startOfYear())->countId(),
                'data' => $data,
            ];
        }

        return $result;
    }

    private function getInstallmentOverdues(): array
    {
        $result = [];
        $queryHasDebt = Installment::query()->where('status', InstallmentStatusEnum::ACTIVE)->whereHasDebt();
        $total_amount = (clone $queryHasDebt)->whereNextPayDate('<=', Carbon::now())->sumDebt();
        $total_count = (clone $queryHasDebt)->whereNextPayDate('<=', Carbon::now())->countId();
        for ($i = 10; $i < 70; $i += 10) {
            $subQuery = (clone $queryHasDebt)->whereNextPayDate('>', Carbon::now()->subDays($i))->whereNextPayDate('<=', Carbon::now()->subDays($i - 10));
            $count = (clone $subQuery)->countId();
            $amount = (clone $subQuery)->sumDebt();
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
}
