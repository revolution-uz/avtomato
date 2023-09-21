<?php

namespace App\Helpers;

use App\Http\Requests\CalculatorRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CalculatorHelper
{

    private function prepareIntallmentGraph(CalculatorRequest $request)
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
            $calculated[] = new IntallmentGraphDTO(
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
}
