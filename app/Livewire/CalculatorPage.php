<?php

namespace App\Livewire;

use App\DTOs\IntallmentGraphDTO;
use App\Http\Requests\CalculatorRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CalculatorPage extends Component
{

    private function prepareIntallmentGraph(CalculatorRequest $request)
    {
        $day = date('d');
        $percent = $request->getTariff()->percent;
        $amount = $request->amount;
        $amount -= $request->initial_amount;
        $portion = $amount * $percent / 100;

        $calculated = [];
        $dateTime = time();

        for ($i = 0; $i < $request->period; $i++) {
            $calculated[] = new IntallmentGraphDTO(
                $i + 1,
                $dateTime,
                $amount / $request->period,
                $portion,
                $amount / $request->period + $portion,
            );
            $dateTime = strtotime($day . date('.m.Y', strtotime(date('d.m.Y', $dateTime)) + 31 * 86400));
        }
        return $calculated;
    }

    public function render(CalculatorRequest $request): View|\Illuminate\Foundation\Application|Factory|Application
    {
        if($request->validate()) {
            ///response, it is not valid
        }


        return view('livewire.calculator', ['calculated' => $this->prepareIntallmentGraph($request)]);
    }
}
