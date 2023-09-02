<?php

namespace App\Http\Controllers\template\pages;

use App\Http\Controllers\Controller;

class MiscError extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.pages.pages-misc-error', ['pageConfigs' => $pageConfigs]);
  }
}
