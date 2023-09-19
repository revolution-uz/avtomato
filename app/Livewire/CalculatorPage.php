<?php

namespace App\Livewire;

use App\DTOs\IntallmentGraphDTO;
use App\Http\Requests\CalculatorRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CalculatorPage extends Component
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

    public function render(CalculatorRequest $request): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.calculator', ['calculated' => $this->prepareIntallmentGraph($request)]);
    }
}
