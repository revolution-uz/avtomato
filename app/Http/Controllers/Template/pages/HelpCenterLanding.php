<?php

namespace App\Http\Controllers\template\pages;

use App\Http\Controllers\Controller;

class HelpCenterLanding extends Controller
{
  public function index()
  {
    return view('content.pages.pages-help-center-landing');
  }
}
