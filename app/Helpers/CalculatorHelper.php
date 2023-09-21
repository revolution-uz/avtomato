<?php

namespace App\Helpers;

use App\DTOs\InstallmentGraphDTO;
use App\Http\Requests\CalculatorRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalculatorHelper
{

    public function prepareIntallmentGraph(CalculatorRequest $request)
    {
        $percent = $request->getTariff()->percent;
        $amount = $request->amount;
        $amount -= $request->initial_amount;
        $portion = $amount * $percent / 100;

        $currentDate = Carbon::now();
        $periods = CarbonPeriod::create($currentDate, '1 month', $currentDate->copy()->addMonths($request->period - 1));

        $calculated = [];
        $i = 1;
        foreach ($periods as $period) {
            $calculated[] = new InstallmentGraphDTO(
                $i,
                $period->format("Y-m-d"),
                $amount / $request->period,
                $portion,
                $amount / $request->period + $portion,
            );
            $i++;
        }
        return $calculated;
    }

    public function prepareIntallmentGraphCSV(CalculatorRequest $request)
    {
        $data = [["ID", "Date", "Amount", "Portion", "Monthly"]];
        foreach ($this->prepareIntallmentGraph($request) as $item)
            $data[] = [$item->id,$item->date,$item->amount, $item->portion, $item->monthly];
        return $data;
    }

}
