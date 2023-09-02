<?php

namespace App\Http\Controllers\template\form_layouts;

use App\Http\Controllers\Controller;

class VerticalForm extends Controller
{
  public function index()
  {
    return view('content.form-layout.form-layouts-vertical');
  }
}
