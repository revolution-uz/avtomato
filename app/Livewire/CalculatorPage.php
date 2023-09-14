<?php

namespace App\Livewire;

use App\Models\Company\Tariffs;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;

class CalculatorPage extends Component
{

    public function render(Request $request): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $tariff_id = $request->get('tariff_id');
        $amount = $request->get('amount');
        $initial_amount = $request->get('initial_amount');
        $period = $request->get('period');
        $day = date('d');
        $percent = Tariffs::query()->where('id', $tariff_id)->first()->percent ?? 0;
        $amount -= $initial_amount;
        $portion = $amount * $percent / 100;
        $calculated = [];
        $dateTime = time();
        for ($i = 0; $i < $period; $i++) {
            $calculated[] = [
                'id' => $i + 1,
                'date' => date("d.m.Y", $dateTime),
                'amount' => $amount / $period,
                'portion' => $portion,
                'monthly' => $amount / $period + $portion,
            ];
            $dateTime = strtotime($day . date('.m.Y', strtotime(date('d.m.Y', $dateTime)) + 31 * 86400));
        }
        return view('livewire.calculator', ['calculated' => $calculated]);
    }
}
