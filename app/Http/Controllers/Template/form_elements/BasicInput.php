<?php

namespace App\Http\Controllers\template\form_elements;

use App\Http\Controllers\Controller;

class BasicInput extends Controller
{
  public function index()
  {
    return view('content.form-elements.forms-basic-inputs');
  }
}
