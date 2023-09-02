<?php

namespace App\Livewire;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class LoginPage extends Component
{
    #[Rule('required')]
    public ?string $username;
    #[Rule('required')]
    public ?string $password;
    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.login-page')->layout('layouts.blankLayout');
    }

    public function login(): void
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if(Auth::attempt(array('username' => $this->username, 'password' => $this->password))){
            $this->redirect('/');
        }else{
            session()->flash('error', 'email and password are wrong.');
        }
    }
}
