<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\NewCustomer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('website.home');
    }

    public function newCustomer(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:16',
        ]);

        $newCustomer = new NewCustomer;
        $newCustomer->full_name = $request->full_name;
        $newCustomer->phone = $request->phone;
        $newCustomer->saveOrFail();

        return redirect()->back()->with('success', true);
    }
}
