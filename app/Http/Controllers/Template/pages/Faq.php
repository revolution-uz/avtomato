<?php

namespace App\Http\Controllers\template\pages;

use App\Http\Controllers\Controller;

class Faq extends Controller
{
  public function index()
  {
    return view('content.pages.pages-faq');
  }
}
