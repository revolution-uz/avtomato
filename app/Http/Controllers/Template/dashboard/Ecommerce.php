<?php

namespace App\Http\Controllers\template\dashboard;

use App\Http\Controllers\Controller;

class Ecommerce extends Controller
{
    public function index(){
      return view('content.dashboard.dashboards-ecommerce');
    }
}
