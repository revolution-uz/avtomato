<?php

namespace App\Http\Controllers\template\pages;

use App\Http\Controllers\Controller;

class MiscComingSoon extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.pages.pages-misc-comingsoon', ['pageConfigs' => $pageConfigs]);
  }
}
