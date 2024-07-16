<?php

namespace App\Services\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Helpers\Utilities;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGraph;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Tariff;
use App\Models\Company\Transaction;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Throwable;

class InstallmentService
{
    private BalanceService $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function create(array $attributes): Installment
    {
        $attributes['status'] = InstallmentStatusEnum::IN_ACTIVE;
        $attributes['admin_id'] = auth()->user()->id;
        $attributes['sms_notify'] = false;
        $attributes['any_card_charge'] = false;
        $attributes['type'] = InstallmentTypeEnum::WITHOUT_CARD;
        $attributes['next_pay_date'] = $attributes['starts_in'];

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

        if ($installment->any_card_charge || $installment->type == InstallmentTypeEnum::WITH_CARD) {
            $this->withdrawAutoCharge($installment);
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
        if ($auto_charge) {
            $installmentGuarantor->any_card_charge = true;
            $this->withdrawAutoCharge($installmentGuarantor->installment);
        } else {
            $installmentGuarantor->any_card_charge = false;
        }
        $installmentGuarantor->saveOrFail();

        return $installmentGuarantor;
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function changeStatus(Installment $installment, InstallmentStatusEnum $status): bool
    {
        if ($status == InstallmentStatusEnum::ACTIVE) {
            $this->running($installment);
        }

        $installment->status = $status;

        return $installment->save();
    }

    public function getDashboardInfo()
    {
        return [
            'installment_amounts' => $this->getInstallmentAmounts(),
            'installment_info_by_status' => $this->getInstallmentInfoByStatus(),
            'installment_overdues' => $this->getInstallmentOverdues(),
        ];
    }

    private function withdrawAutoCharge(Installment $installment)
    {
        if (! $installment->withdrawn_auto_charge) {
            $this->balanceService->chargeAmount(FeatureEnum::INSTALLMENT_AUTO_CHARGE, $installment);
            $installment->withdrawn_auto_charge = true;
        }
        $installment->saveOrFail();
    }

    /**
     * @throws BalanceIsNotEnoughException
     */
    private function running(Installment $installment): void
    {
        if (is_array($installment->graphs) && count($installment->graphs)) {
            return;
        }
        $this->balanceService->chargeAmount(PermissionEnum::getFeature(PermissionEnum::INSTALLMENT_START), $installment);
        /** @var Tariff $tariff */
        $tariff = Tariff::findOrFail($installment->tariff_id);
        $amount = $installment->amount;
        $amount -= $installment->initial_fee;
        $portion = $amount * $tariff->percent / 100 / $installment->period;

        $currentDate = new Carbon;
        $currentDate->timestamp = $installment->starts_in->timestamp;
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($installment->period - 1));

        foreach ($periods as $period) {
            $installmentGraph = new InstallmentGraph;
            $installmentGraph->installment_id = $installment->id;
            $installmentGraph->amount = $amount / $installment->period + $portion;
            $installmentGraph->starts_in = $period->format('Y-m-d');
            $installmentGraph->status = InstallmentStatusEnum::ACTIVE;
            $installmentGraph->paid_amount = 0;
            $installmentGraph->save();
        }
        $installment->setNextPayDate();
        $installment->save();
    }

    private function getInstallmentAmounts(): array
    {
        return [
            'transaction' => $this->calculateByIncreasingPercent(Transaction::query(), 'id', 'amount'),
            'installment' => $this->calculateByIncreasingPercent(Installment::query(), 'id', 'amount'),
            'installment_future_profit' => $this->calculateByIncreasingPercent(Installment::join('tariffs', 'installments.tariff_id', '=', 'tariffs.id')
                ->where('status', '>', InstallmentStatusEnum::IN_ACTIVE), 'installments.id', DB::raw('installments.amount / (1 + tariffs.percent / 100)'), 'installments.created_at'),
            'installment_overdue' => $this->calculateByIncreasingPercent(Installment::join('installment_graphs', 'installments.id', '=', 'installment_graphs.installment_id')
                ->where('installment_graphs.starts_in', '<', Carbon::now()), 'installments.id', DB::raw('installment_graphs.amount - installment_graphs.paid_amount'), 'installments.created_at'),
            'npl' => $this->calculateNpl(60, 7),
            'enpl' => $this->calculateNpl(30, 7),

        ];
    }

    private function getInstallmentOverdues(): array
    {
        $result = [];
        $query = InstallmentGraph::query()->where(DB::raw('amount - paid_amount'), '>', 0);
        $total_amount = (clone $query)->where('starts_in', '<=', Carbon::now())->sum(DB::raw('amount - paid_amount'));
        $total_count = (clone $query)->where('starts_in', '<=', Carbon::now())->count('id');
        for ($i = 10; $i < 70; $i += 10) {
            $subQuery = (clone $query)->where('starts_in', '>', Carbon::now()->subDays($i))->where('starts_in', '<=', Carbon::now()->subDays($i - 10));
            $count = (clone $subQuery)->count('id');
            $amount = (clone $subQuery)->sum('amount');
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
                'name' => $status->getLabel(),
                'type' => 'line',
                'stack' => 'Total',
                'count' => (clone $query)->where('created_at', '>=', Carbon::today()->startOfWeek())->count('id'),
                'data' => $data,
            ];
        }

        return $result;
    }

    private function calculateByIncreasingPercent(\Illuminate\Database\Eloquent\Builder $query, \Illuminate\Contracts\Database\Query\Expression|string $fieldCount, \Illuminate\Contracts\Database\Query\Expression|string $fieldAmount, string $created_at = 'created_at'): array
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
        $query = InstallmentGraph::query();
        $installment_total_amount = Installment::query()->sum('amount');
        $nplQuery = (clone $query)->where('starts_in', '<', Carbon::now()->subDays($overdueDays));
        $nplBeforeWeekQuery = (clone $query)->where('starts_in', '<', Carbon::now()->subDays($overdueDays - $beforeDays));
        $npl = $nplQuery->sum(DB::raw('installment_graphs.amount - installment_graphs.paid_amount')) / $installment_total_amount * 100;
        $nplBefore = $nplBeforeWeekQuery->sum(DB::raw('installment_graphs.amount - installment_graphs.paid_amount')) / $installment_total_amount * 100;

        return [
            'value' => $npl,
            'value_of_last_week' => $nplBefore,
            'value_increasing_percent' => max(0, Utilities::preventDivideByZero($npl, $nplBefore) * 100 - 100),
        ];
    }
}
