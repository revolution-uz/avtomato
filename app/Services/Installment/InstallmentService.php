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
        $attributes['status'] = InstallmentStatusEnum::IN_ACTIVE;
        $attributes['admin_id'] = auth()->user()->id;
        $attributes['sms_notify'] = false;
        $attributes['any_card_charge'] = false;
        $attributes['type'] = InstallmentTypeEnum::WITHOUT_CARD;
        $attributes['percent'] = Tariff::findOrFail($attributes['tariff_id'])->percent;

        return Installment::create($attributes);
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleAutoCharge(Installment $installment, bool $auto_charge, bool $any_card): Installment
    {
        if ($auto_charge) {
            $installment->type = InstallmentTypeEnum::WITH_CARD;
        } else {
            $installment->type = InstallmentTypeEnum::WITHOUT_CARD;
        }
        if ($any_card) {
            $installment->any_card_charge = $any_card;
        }
        if ($this->withdrawAutoCharge($installment)) {
            $installment->saveOrFail();
        }

        return $installment;
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleGuarantorAutoCharge(InstallmentGuarantor $installmentGuarantor, bool $auto_charge): InstallmentGuarantor
    {
        if ($auto_charge) {
            $installmentGuarantor->any_card_charge = true;
            if ($this->withdrawAutoCharge($installmentGuarantor->installment)) {
                $installmentGuarantor->saveOrFail();
            }
        } else {
            $installmentGuarantor->any_card_charge = false;
            $installmentGuarantor->saveOrFail();
        }

        return $installmentGuarantor;
    }

    /**
     * @throws Throwable
     */
    public function changeStatus(Installment $installment, InstallmentStatusEnum $status): bool
    {
        if ($status == InstallmentStatusEnum::ACTIVE) {
            if ($installment->isPaidInitialFee()) {
                if ($this->withdrawAutoCharge($installment)) {
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

    private function withdrawAutoCharge(Installment $installment): bool
    {
        if (($installment->any_card_charge || $installment->auto_pay) && ! $installment->withdrawn_auto_charge) {
            if ($this->balanceService->isBalanceEnoughForFeature(FeatureEnum::INSTALLMENT_AUTO_CHARGE)) {
                $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE, $installment);
                $installment->withdrawn_auto_charge = true;

                return $installment->saveOrFail();
            }

            return false;
        }

        return true;
    }

    private function getInstallmentAmounts(): array
    {
        $nextPayDateSql = DB::raw("starts_in + (FLOOR(paid_amount / (amount - initial_fee) * (percent * 100) / 100 / period)::text || ' month')::interval");

        return [
            'transaction' => $this->calculateByIncreasingPercent(Transaction::query(), 'id', 'amount'),
            'installment' => $this->calculateByIncreasingPercent(Installment::query(), 'id', 'amount'),
            'installment_future_profit' => $this->calculateByIncreasingPercent(
                Installment::query()->join('tariffs', 'installments.tariff_id', '=', 'tariffs.id')
                    ->where('status', '>', InstallmentStatusEnum::IN_ACTIVE),
                'installments.id',
                DB::raw('installments.amount / (1 + tariffs.percent / 100)'),
                'installments.created_at'
            ),
            'installment_overdue' => $this->calculateByIncreasingPercent(
                Installment::query()
                    ->where($nextPayDateSql, '<', Carbon::now()),
                'installments.id',
                DB::raw('(amount - initial_fee) * (percent + 100) / 100 + initial_fee - paid_amount'),
                'installments.created_at'
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
        $installment_total_amount = Installment::query()->sum(DB::raw('amount - initial_fee'));
        $nextPayDateSql = DB::raw("starts_in + (FLOOR(paid_amount / (amount - initial_fee) * (percent * 100) / 100 / period)::text || ' month')::interval");
        $profitSql = DB::raw('(amount - initial_fee) * percent / 100 - paid_amount');
        $nplQuery = (clone $query)->where($nextPayDateSql, '<', Carbon::now()->subDays($overdueDays));
        $nplBeforeWeekQuery = (clone $query)->where($nextPayDateSql, '<', Carbon::now()->subDays($overdueDays - $beforeDays));
        $npl = $installment_total_amount === 0 ? 0 : $nplQuery->sum($profitSql) / $installment_total_amount * 100;
        $nplBefore = $installment_total_amount === 0 ? 0 : $nplBeforeWeekQuery->sum($profitSql) / $installment_total_amount * 100;

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
                'count' => (clone $query)->where('created_at', '>=', Carbon::today()->startOfWeek())->count('id'),
                'data' => $data,
            ];
        }

        return $result;
    }

    private function getInstallmentOverdues(): array
    {
        $result = [];
        $query = Installment::query()->where(DB::raw('(amount - initial_fee) * (percent + 100) / 100 - paid_amount'), '>', 0);
        $nextPayDateSql = DB::raw("starts_in + (FLOOR(paid_amount / (amount - initial_fee) * (percent * 100) / 100 / period)::text || ' month')::interval");
        $total_amount = (clone $query)->where($nextPayDateSql, '<=', Carbon::now())->sum(DB::raw('(amount - initial_fee) * percent / 100 - paid_amount'));
        $total_count = (clone $query)->where($nextPayDateSql, '<=', Carbon::now())->count('id');
        for ($i = 10; $i < 70; $i += 10) {
            $subQuery = (clone $query)->where($nextPayDateSql, '>', Carbon::now()->subDays($i))->where($nextPayDateSql, '<=', Carbon::now()->subDays($i - 10));
            $count = (clone $subQuery)->count('id');
            $amount = (clone $subQuery)->sum(DB::raw('(amount - initial_fee) * (percent + 100) / 100'));
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
