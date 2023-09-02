<?php

namespace App\Http\Controllers\template\user_interface;

use App\Http\Controllers\Controller;

class Progress extends Controller
{
  public function index()
  {
    return view('content.user-interface.ui-progress');
  }
}
