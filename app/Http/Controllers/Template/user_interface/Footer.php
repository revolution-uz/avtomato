<?php

namespace App\Http\Controllers\template\user_interface;

use App\Http\Controllers\Controller;

class Footer extends Controller
{
  public function index()
  {
    return view('content.user-interface.ui-footer');
  }
}
