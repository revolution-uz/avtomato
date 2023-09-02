<?php

namespace App\Http\Controllers\template\apps;

use App\Http\Controllers\Controller;

class InvoicePrint extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.apps.app-invoice-print', ['pageConfigs' => $pageConfigs]);
  }
}
