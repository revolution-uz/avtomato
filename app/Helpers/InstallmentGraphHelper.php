<?php

namespace App\Helpers;

use App\DTOs\Installment\InstallmentGraphDTO;
use App\Enums\Company\InstallmentGraphStatusEnum;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Services\Installment\InstallmentService;
use Illuminate\Support\Carbon;

class InstallmentGraphHelper
{
    /**
     * @return InstallmentGraphDTO[]
     */
    public static function getGraphs(Installment $installment): array
    {
        $transactions = $installment->transactions()->orderBy('id')->select('amount', 'created_at')->get();
        $paidAmount = max(round($transactions->sum('amount') - $installment->initial_fee, 2), 0);
        $graphs = [];
        $months = InstallmentService::generatePeriods($installment->starts_in, $installment->period);
        /** @var Carbon $monthlyPaymentDate */
        foreach ($months as $index => $monthlyPaymentDate) {
            $paidForMonth = round(min($paidAmount, $installment->monthlyPayment), 2);
            $paidAmount -= $paidForMonth;
            $paidDateForMonth = Carbon::now();
            $paidSumBeforeNow = 0;
            foreach ($transactions as $transaction) {
                /** @var Transaction $transaction */
                $paidSumBeforeNow += $transaction->amount;
                if (round(($index + 1) * $installment->monthlyPayment + $installment->initial_fee) <= round($paidSumBeforeNow)) {
                    $paidDateForMonth = $transaction->created_at;
                    break;
                }
            }
            $graphs[] = new InstallmentGraphDTO(
                id: $index + 1,
                amount: round($installment->monthlyPayment, 2),
                paid_amount: $paidForMonth,
                overdue_days: $monthlyPaymentDate->isPast() ? max(round($monthlyPaymentDate->diffInDays($paidDateForMonth)), 0) : 0,
                starts_in: $monthlyPaymentDate,
                status: $monthlyPaymentDate->isPast()
                    ? (
                        $paidForMonth >= $installment->monthlyPayment
                            ? InstallmentGraphStatusEnum::PAID
                            : InstallmentGraphStatusEnum::DELAY
                    ) : InstallmentGraphStatusEnum::NOT_STARTED
            );
        }

        return $graphs;
    }
}
