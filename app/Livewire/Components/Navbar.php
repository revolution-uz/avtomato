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
    public function logout(): void
    {
       Auth::logout();
       $this->redirect('/');
    }

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.components.navbar', ['tariffs' => Tariffs::query()->all()]);
    }
}
