<?php

namespace App\Livewire\Components;

use App\Models\Company\Tariffs;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navbar extends Component
{

    public $tariff_id;
    public $amount;
    public $initial_amount;
    public $period;
    public $calculated = [];
    public $is_show_calculator_modal = false;

    public function calculator(): void
    {
        $day = date('d');
        $percent = Tariffs::query()->where('id', $this->tariff_id)->first()->percent ?? 0;
        $this->amount -= $this->initial_amount;
        $portion = $this->amount * $percent / 100;
        for ($i = 0; $i < $this->period; $i++)
            $this->calculated[] = [
                'id' => $i + 1,
                'date' => $day . date('.m.Y', strtotime(date('d.m.Y')) + 31 * 86400),
                'amount' => $this->amount / $this->period,
                'portion' => $portion,
                'monthly' => $this->amount / $this->period + $portion,
            ];
        $this->is_show_calculator_modal = true;
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/');
    }

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.components.navbar', ['tariffs' => Tariffs::query()->get()]);
    }
}
