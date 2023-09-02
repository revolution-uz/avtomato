<?php

namespace App\Http\Controllers\template\charts;

use App\Http\Controllers\Controller;

class ApexCharts extends Controller
{
  public function index()
  {
    return view('content.charts.charts-apex');
  }
}
