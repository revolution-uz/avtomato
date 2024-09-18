<?php

namespace App\Services\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Helpers\Utilities;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Tariff;
use App\Models\Company\Transaction;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
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
    public function toggleAutoAndAnyCardCharge(Installment $installment, bool $auto_charge, bool $any_card): Installment
    {
        if ($auto_charge && ! ($installment->type->value & InstallmentTypeEnum::WITH_CARD->value)) {
            $installment->type = InstallmentTypeEnum::from($installment->type->value | InstallmentTypeEnum::WITH_CARD->value);
            if (! $this->withdrawAutoCharge($installment)) {
                throw ValidationException::withMessages(['type' => __('api.You do not have enough balance to update installment type')]);
            }
        }
        if ($any_card && ! ($installment->type->value & InstallmentTypeEnum::WITH_PINFL->value)) {
            $installment->type = InstallmentTypeEnum::from($installment->type->value | InstallmentTypeEnum::WITH_PINFL->value);
            $installment->any_card_charge = $any_card;
            if (! $this->withdrawAnyCardCharge($installment)) {
                throw ValidationException::withMessages(['type' => __('api.You do not have enough balance to update installment type')]);
            }
        }
        $installment->saveOrFail();

        return $installment;
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleGuarantorAutoCharge(InstallmentGuarantor $installmentGuarantor, bool $auto_charge): InstallmentGuarantor
    {
        $installmentGuarantor->any_card_charge = $auto_charge;
        if (! $auto_charge) {
            $installmentGuarantor->saveOrFail();

            return $installmentGuarantor;
        }
        $this->toggleAutoAndAnyCardCharge($installmentGuarantor->installment, false, true);
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
                if ($this->withdrawAutoCharge($installment) && $this->withdrawAnyCardCharge($installment)) {
                    $installment->status = $status;
                }

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
                    WHERE status = 1 {$branches_in_query}
                ),
                installment_calculations AS (
                    SELECT payment_date,
                        CASE WHEN payment_date > NOW() THEN 0 ELSE LEAST(debt_amount, monthly_payment) END AS debt_amount,
                        ABS(LEAST(debt_amount, monthly_payment) - monthly_payment) as paid_amount
                    FROM installment_precalculations
                )
            SELECT
                TO_CHAR(date, 'DD-MM-YYYY') AS date, COALESCE(SUM(debt_amount), 0) AS debt_amount_sum, COALESCE(SUM(paid_amount), 0) AS paid_amount_sum
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
                FROM installments WHERE status = 1 {$branches_in_query}
            )
            SELECT
                i.id,
                b.name AS branch_name,
                c.last_name || ' ' || c.first_name || ' ' || c.middle_name AS customer_fullname,
                CASE WHEN '{$date}'::date > NOW() THEN 0 ELSE LEAST(GREATEST(i.monthly_payment * LEAST(i.age + 1, i.period) - i.paid_amount, 0), i.monthly_payment) END AS debt_amount,
                ABS(LEAST(GREATEST(i.monthly_payment * LEAST(i.age + 1, i.period) - i.paid_amount, 0), i.monthly_payment) - i.monthly_payment) as paid_amount
            FROM installments_optimized i
                LEFT JOIN branches b ON b.id = i.branch_id
                LEFT JOIN customers c ON c.id = i.customer_id
            WHERE i.starts_in + (i.age || ' month')::interval = '{$date}'::date
              AND i.age BETWEEN 0 AND i.period - 1
            ORDER BY i.id
        ");
    }

    public function statisticsByBranches(?Carbon $date = null, ?array $branch_ids = null): array
    {
        $branchesQuery = Installment::query()
            ->select('branch_id', 'type', DB::raw('SUM(paid_amount) as total_amount'))
            ->when(! empty($branch_ids), fn ($q) => $q->whereIn('branch_id', $branch_ids))
            ->when(! empty($date), fn ($q) => $q->whereDate('created_at', '=', $date))
            ->groupBy('branch_id', 'type')
            ->orderBy('branch_id')
            ->get();
        $statistics = [];
        $totalAmountByType = [];
        $formattedTotalAmountByType = [];
        $fullTotalAmount = 0;

        foreach ($branchesQuery as $branch) {
            $branchId = $branch->branch_id;
            $type = $branch->type->value;
            /* @phpstan-ignore-next-line **/
            $totalAmount = $branch->total_amount;

            if (! isset($statistics[$branchId])) {
                $statistics[$branchId] = [
                    'branch_id' => $branchId,
                    'branch_name' => $branch->branch->name,
                    'types' => [],
                    'total_amount_by_branch' => 0,
                ];
            }

            $statistics[$branchId]['types'][] = [
                'type' => $type,
                'total_amount' => $totalAmount,
            ];

            $statistics[$branchId]['total_amount_by_branch'] += $totalAmount;

            if (! isset($totalAmountByType[$type])) {
                $totalAmountByType[$type] = 0;
            }
            $totalAmountByType[$type] += $totalAmount;

            $formattedTotalAmountByType[$type] = [
                'type' => $type,
                'total_amount' => $totalAmountByType[$type],
            ];

            $fullTotalAmount += $totalAmount;
        }

        return [
            'branches' => $statistics,
            'total_amount_by_type' => array_values($formattedTotalAmountByType),
            'full_total_amount' => number_format($fullTotalAmount, 2, '.', '.'),
        ];
    }

    private function withdrawAutoCharge(Installment $installment): bool
    {
        if ($this->balanceService->isBalanceEnoughForFeature(FeatureEnum::INSTALLMENT_AUTO_CHARGE)) {
            $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE, $installment);

            return true;
        }

        return false;
    }

    private function withdrawAnyCardCharge(Installment $installment): bool
    {
        if ($this->balanceService->isBalanceEnoughForFeature(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)) {
            $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL, $installment);

            return true;
        }

        return false;
    }

    private function getInstallmentAmounts(): array
    {
        return [
            'transaction' => $this->calculateByIncreasingPercent(Transaction::query(), 'id', 'amount'),
            'installment' => $this->calculateByIncreasingPercent(Installment::query(), 'id', 'amount'),
            'installment_future_profit' => $this->calculateByIncreasingPercent(
                Installment::query()->where('status', '>', InstallmentStatusEnum::INACTIVE),
                'id',
                Installment::getProfitAmountRaw()
            ),
            'installment_overdue' => $this->calculateByIncreasingPercent(
                Installment::query()
                    ->whereNextPayDate('<', Carbon::now()),
                'id',
                Installment::getDebtAmountRaw()
            ),
            'npl' => $this->calculateNpl(60, 7),
            'enpl' => $this->calculateNpl(30, 7),

        ];
    }

    private function calculateByIncreasingPercent(Builder $query, Expression|string $fieldCount, Expression|string $fieldAmount, string $created_at = 'created_at'): array
    {
        $beforeWeekQuery = (clone $query)->where($created_at, '<', Carbon::today()->startOfWeek());
        $count = $query->count($fieldCount);
        $beforeWeekCount = $beforeWeekQuery->count($fieldCount);
        $amount = $query->sum($fieldAmount);
        $beforeWeekAmount = $beforeWeekQuery->sum($fieldAmount);

        return [
            'count' => $count,
            'count_increasing_percent' => max(0, Utilities::preventDivideByZero($count, $beforeWeekCount) * 100 - 100),
            'amount' => $amount,
            'amount_increasing_percent' => max(0, Utilities::preventDivideByZero($amount, $beforeWeekAmount) * 100 - 100),
        ];
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
                'count' => (clone $query)->where('created_at', '>=', Carbon::today()->startOfWeek())->countId(),
                'data' => $data,
            ];
        }

        return $result;
    }

    private function getInstallmentOverdues(): array
    {
        $result = [];
        $queryHasDebt = Installment::query()->whereHasDebt();
        $total_amount = (clone $queryHasDebt)->whereNextPayDate('<=', Carbon::now())->sumDebt();
        $total_count = (clone $queryHasDebt)->whereNextPayDate('<=', Carbon::now())->countId();
        for ($i = 10; $i < 70; $i += 10) {
            $subQuery = (clone $queryHasDebt)->whereNextPayDate('>', Carbon::now()->subDays($i))->whereNextPayDate('<=', Carbon::now()->subDays($i - 10));
            $count = (clone $subQuery)->countId();
            $amount = (clone $subQuery)->sumInstallmentAmount();
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
