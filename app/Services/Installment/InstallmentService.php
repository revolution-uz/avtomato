<?php

namespace App\Services\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Helpers\Utilities;
use App\Http\Requests\Installment\InstallmentToggleRequest;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGraph;
use App\Models\Company\InstallmentGuarantor;
use App\Models\Company\Tariff;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
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
        $attributes['status'] = InstallmentStatusEnum::DRAFT;
        $attributes['user_id'] = auth()->user()->id;
        $attributes['sms_notify'] = false;
        $attributes['any_card_charge'] = false;
        $attributes['type'] = InstallmentTypeEnum::WITHOUT_CARD;

        return Installment::create($attributes);
    }

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function toggleAutoCharge(Installment $installment, InstallmentToggleRequest $request): Installment
    {
        if ($request->auto_charge) {
            $installment->type = InstallmentTypeEnum::WITH_CARD;
        } else {
            $installment->type = InstallmentTypeEnum::WITHOUT_CARD;
        }
        if (isset($request->any_card)) {
            $installment->any_card_charge = $request->any_card;
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
    public function toggleGuarantorAutoCharge(InstallmentGuarantor $installmentGuarantor, InstallmentToggleRequest $request): InstallmentGuarantor
    {
        if ($request->auto_charge) {
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
        if ($status == InstallmentStatusEnum::RUNNING) {
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
        $this->balanceService->chargeAmount(PermissionEnum::getFeature(PermissionEnum::INSTALLMENT_START), $installment);
        /** @var Tariff $tariff */
        $tariff = Tariff::findOrFail($installment->tariff_id);
        $amount = $installment->amount;
        $amount -= $installment->initial_fee;
        $portion = $amount * $tariff->percent / 100;

        $currentDate = new Carbon;
        $currentDate->timestamp = $installment->starts_in->timestamp;
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($installment->period - 1));

        foreach ($periods as $period) {
            $installmentGraph = new InstallmentGraph;
            $installmentGraph->installment_id = $installment->id;
            $installmentGraph->amount = $amount / $installment->period + $portion;
            $installmentGraph->starts_in = Carbon::make($period->timestamp);
            $installmentGraph->status = InstallmentStatusEnum::RUNNING;
            $installmentGraph->paid_amount = 0;
            $installmentGraph->save();
        }
    }

    private function getInstallmentAmounts(): array
    {
        $query = Installment::query();
        $installment_total_amount = $query->sum('amount');
        $lastMonthQuery = Installment::query()->where('created_at', '>=', Carbon::now()->startOfMonth());
        $overdueQuery = Installment::join('installment_graphs', 'installments.id', '=', 'installment_graphs.installment_id')
            ->where('installment_graphs.starts_in', '<', Carbon::now());
        $nplQuery = InstallmentGraph::query()->where('starts_in', '<', Carbon::now()->subDays(60));
        $eNplQuery = InstallmentGraph::query()->where('starts_in', '<', Carbon::now()->subDays(30));

        return [
            'installment_count' => $lastMonthQuery->count('id'),
            'installment_amount' => $lastMonthQuery->sum('amount'),
            'installment_future_profit' => Installment::join('tariffs', 'installments.tariff_id', '=', 'tariffs.id')->sum('installments.amount / (1 + tariffs.percent / 100))'),
            'installment_total_amount' => $installment_total_amount,
            'installment_overdue_count' => $overdueQuery->count('installments.id'),
            'installment_overdue_amount' => $overdueQuery->sum('installment_graphs.amount - installment_graphs.paid_amount'),
            'npl' => $nplQuery->sum('installment_graphs.amount - installment_graphs.paid_amount') / $installment_total_amount * 100,
            'enpl' => $eNplQuery->sum('installment_graphs.amount - installment_graphs.paid_amount') / $installment_total_amount * 100,

        ];
    }

    private function getInstallmentOverdues(): array
    {
        $result = [];
        $query = InstallmentGraph::query()->where('amount - paid_amount', '>', 0);
        $total_amount = (clone $query)->where('starts_in', '<=', Carbon::now())->sum('amount - paid_amount');
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
        $monthNames = Utilities::getMonthNames();
        $result = [];
        for ($i = 0; $i < 12; $i++) {
            $data = [];
            $query = Installment::query()->whereBetween('created_at', [
                Carbon::createFromTimestamp($timestamps[$i]['start']),
                Carbon::createFromTimestamp($timestamps[$i]['end']),
            ]);
            foreach (InstallmentStatusEnum::values() as $value) {
                $status = InstallmentStatusEnum::tryFrom($value);
                $data[] = [
                    'status' => $status->name,
                    'count' => (clone $query)->where('status', $status)->count('id'),
                ];
            }
            $result[] = [
                'month_name' => $monthNames[$i],
                'timestamp' => $timestamps[$i]['start'],
                'data' => $data,
            ];

        }

        return $result;
    }
}
