<?php

namespace Tests\Unit\Helpers;

use App\Enums\Company\InstallmentGraphStatusEnum;
use App\Helpers\InstallmentGraphHelper;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Services\Installment\InstallmentService;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstallmentGraphHelperTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function getInstallmentGraphForNewInstallment(): void
    {
        Transaction::query()->delete();
        $installment = Installment::factory()->create(['starts_in' => Carbon::now()->addMonth()]);
        $graphs = InstallmentGraphHelper::getGraphs($installment);
        $months = InstallmentService::generatePeriods($installment->starts_in, $installment->period);

        $this->assertEquals($installment->period, count($graphs));
        foreach ($graphs as $index => $graph) {
            $this->assertEquals(InstallmentGraphStatusEnum::NOT_STARTED, $graph->status);
            $this->assertEquals(0, $graph->paid_amount);
            $this->assertEquals(round(($installment->amount - $installment->initial_fee) * $installment->portion / $installment->period, 2), $graph->amount);
            $this->assertEquals($months[$index], $graph->starts_in);
            $this->assertEquals(0, $graph->overdue_days);
        }
    }

    #[Test]
    public function getInstallmentGraphForNewInstallmentWithInitialFee(): void
    {
        Transaction::query()->delete();
        $installment = Installment::factory()->create(['starts_in' => Carbon::now()->addMonth()]);
        $graphs = InstallmentGraphHelper::getGraphs($installment);
        // initial fee
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'created_at' => $installment->starts_in->subMonths(4)->subDays(5)]);
        $months = InstallmentService::generatePeriods($installment->starts_in, $installment->period);

        $this->assertEquals($installment->period, count($graphs));
        foreach ($graphs as $index => $graph) {
            $this->assertEquals(InstallmentGraphStatusEnum::NOT_STARTED, $graph->status);
            $this->assertEquals(0, $graph->paid_amount);
            $this->assertEquals(round(($installment->amount - $installment->initial_fee) * $installment->portion / $installment->period, 2), $graph->amount);
            $this->assertEquals($months[$index], $graph->starts_in);
            $this->assertEquals(0, $graph->overdue_days);
        }
    }

    #[Test]
    public function getInstallmentGraphForLastMonthPaymentInstallment(): void
    {
        Transaction::query()->delete();
        $installment = Installment::factory()->create(['starts_in' => Carbon::now()->subMonths(2)->subDays(5), 'period' => 3]);
        $amountPerMonth = round((($installment->amount - $installment->initial_fee) * $installment->portion) / $installment->period, 2);
        // initial fee
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'created_at' => $installment->starts_in->subMonths(4)->subDays(5)]);
        // first month at time, one day before
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth, 2), 'created_at' => $installment->starts_in->subDay()]);
        // second month with three days delay
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth / 2, 2), 'created_at' => $installment->starts_in->addMonth()->addDays(2)]);
        $secondMonthTransaction = Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth / 2, 2), 'created_at' => $installment->starts_in->addMonth()->addDays(3)]);
        // third month not paid yet
        $graphs = InstallmentGraphHelper::getGraphs($installment);
        $monthlyPaymentDates = InstallmentService::generatePeriods($installment->starts_in, $installment->period);

        // first month check
        $this->assertEquals($installment->period, count($graphs));
        $this->assertEquals(InstallmentGraphStatusEnum::PAID, $graphs[0]->status);
        $this->assertEquals($amountPerMonth, $graphs[0]->paid_amount);
        $this->assertEquals($amountPerMonth, $graphs[0]->amount);
        $this->assertEquals($monthlyPaymentDates[0], $graphs[0]->starts_in);
        $this->assertEquals(0, $graphs[0]->overdue_days);

        // second month check
        $this->assertEquals(InstallmentGraphStatusEnum::PAID, $graphs[1]->status);
        $this->assertEquals($amountPerMonth, $graphs[1]->paid_amount);
        $this->assertEquals($amountPerMonth, $graphs[1]->amount);
        $this->assertEquals($monthlyPaymentDates[1], $graphs[1]->starts_in);
        $this->assertEquals(round($monthlyPaymentDates[1]?->diffInDays($secondMonthTransaction->created_at)), $graphs[1]->overdue_days);

        // third month check
        $this->assertEquals(InstallmentGraphStatusEnum::DELAY, $graphs[2]->status);
        $this->assertLessThanOrEqual(0.01, $graphs[2]->paid_amount);
        $this->assertEquals($amountPerMonth, $graphs[2]->amount);
        $this->assertEquals($monthlyPaymentDates[2], $graphs[2]->starts_in);
        $this->assertEquals(round($monthlyPaymentDates[2]?->diffInDays(Carbon::now())), $graphs[2]->overdue_days);
    }

    #[Test]
    public function getInstallmentGraphForWellClosedInstallment(): void
    {
        Transaction::query()->delete();
        $installment = Installment::factory()->create(['starts_in' => Carbon::now()->subMonths(2)->subDays(5), 'period' => 3]);
        $amountPerMonth = round((($installment->amount - $installment->initial_fee) * $installment->portion) / $installment->period, 2);
        // initial fee
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'created_at' => $installment->starts_in->subMonths(4)->subDays(5)]);
        // first month at time, one day before
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth, 2), 'created_at' => $installment->starts_in->subDay()]);
        // second month with two parts
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth / 2, 2), 'created_at' => $installment->starts_in->addMonth()->subDays(2)]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth / 2, 2), 'created_at' => $installment->starts_in->addMonth()->subDay()]);
        // third month paid 15 days early
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => round($amountPerMonth, 2), 'created_at' => Carbon::now()->subDays(20)]);
        $graphs = InstallmentGraphHelper::getGraphs($installment);
        $monthlyPaymentDates = InstallmentService::generatePeriods($installment->starts_in, $installment->period);

        // first month check
        $this->assertEquals($installment->period, count($graphs));
        $this->assertEquals(InstallmentGraphStatusEnum::PAID, $graphs[0]->status);
        $this->assertEquals($amountPerMonth, $graphs[0]->paid_amount);
        $this->assertEquals($amountPerMonth, $graphs[0]->amount);
        $this->assertEquals($monthlyPaymentDates[0], $graphs[0]->starts_in);
        $this->assertEquals(0, $graphs[0]->overdue_days);

        // second month check
        $this->assertEquals(InstallmentGraphStatusEnum::PAID, $graphs[1]->status);
        $this->assertEquals($amountPerMonth, $graphs[1]->paid_amount);
        $this->assertEquals($amountPerMonth, $graphs[1]->amount);
        $this->assertEquals($monthlyPaymentDates[1], $graphs[1]->starts_in);
        $this->assertEquals(0, $graphs[1]->overdue_days);

        // third month check
        $this->assertEquals(InstallmentGraphStatusEnum::PAID, $graphs[2]->status);
        $this->assertEquals($amountPerMonth, $graphs[2]->paid_amount);
        $this->assertEquals($amountPerMonth, $graphs[2]->amount);
        $this->assertEquals($monthlyPaymentDates[2], $graphs[2]->starts_in);
        $this->assertEquals(0, $graphs[2]->overdue_days);
    }
}
