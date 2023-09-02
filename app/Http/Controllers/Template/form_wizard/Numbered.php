<?php

namespace App\Http\Controllers\template\form_wizard;

use App\Http\Controllers\Controller;

class Numbered extends Controller
{
  public function index()
  {
    return view('content.form-wizard.form-wizard-numbered');
  }
}
