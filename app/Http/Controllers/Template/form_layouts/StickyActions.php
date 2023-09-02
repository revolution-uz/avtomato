<?php

namespace App\Http\Controllers\template\form_layouts;

use App\Http\Controllers\Controller;

class StickyActions extends Controller
{
  public function index()
  {
    return view('content.form-layout.form-layouts-sticky');
  }
}
