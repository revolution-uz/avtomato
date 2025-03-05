<?php

namespace App\Helpers;

use App\DTOs\Installment\InstallmentGraphDTO;
use App\Enums\Company\InstallmentGraphStatusEnum;
use App\Enums\Company\TransactionStatusEnum;
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
        $transactions = $installment->transactions()->where('status', TransactionStatusEnum::SUCCESS)->orderBy('id')->select('amount', 'created_at')->get();
        $paidAmount = max(round($transactions->sum('amount') - $installment->initial_fee, 2), 0);
        $graphs = [];
        $months = InstallmentService::generatePeriods($installment->starts_in, $installment->period);
        $allPaid = $paidAmount >= $installment->installment_amount;
        /** @var Carbon $monthlyPaymentDate */
        foreach ($months as $index => $monthlyPaymentDate) {
            $paidForMonth = round(min($paidAmount, $installment->monthlyPayment), 2);
            $paidAmount -= $paidForMonth;
            $paidDateForMonth = Carbon::now();
            $paidSumBeforeNow = 0;
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $paidSumBeforeNow += $transaction->amount;
                if (round(($index + 1) * $installment->monthlyPayment + $installment->initial_fee) <= round($paidSumBeforeNow)) {
                    $paidDateForMonth = $transaction->created_at;
                    break;
                }
            }
            $isOverdue = $monthlyPaymentDate->diffInDays() >= 1;
            $status = $allPaid || $paidForMonth >= $installment->monthlyPayment
                ? InstallmentGraphStatusEnum::PAID
                : ($isOverdue ? InstallmentGraphStatusEnum::DELAY : InstallmentGraphStatusEnum::NOT_STARTED);
            $graphs[] = new InstallmentGraphDTO(
                id: $index + 1,
                amount: $installment->monthlyPayment,
                paid_amount: $paidForMonth,
                overdue_days: $isOverdue ? max(round($monthlyPaymentDate->diffInDays($paidDateForMonth)), 0) : 0,
                starts_in: $monthlyPaymentDate,
                status: $status
            );
        }

        return $graphs;
    }
}
