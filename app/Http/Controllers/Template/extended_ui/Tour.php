<?php

namespace App\Http\Controllers\template\extended_ui;

use App\Http\Controllers\Controller;

class Tour extends Controller
{
  public function index()
  {
    return view('content.extended-ui.extended-ui-tour');
  }
}
