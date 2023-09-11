<?php

namespace App\Livewire\Components;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Calculator extends Component
{

    public $calculated = [];
    public $is_show_calculator_modal = false;

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.components.calculator');
    }
}
